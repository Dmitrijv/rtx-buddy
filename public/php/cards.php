<?php

header('Content-Type: application/json');

abstract class ProductStatus {
  const KnownStock = 0;
  const InStock = 1;
  const Incoming = 2;
  const Delayed = 3;
  const SoldOut = 4;
  const Blocked = 5;
  const Na = 6;
}

$inetCardIds = array(
  '5411691', //Gigabyte Geforce RTX 3080 10GB EAGLE OC
  '5411768', //Gigabyte GeForce RTX 3080 10GB AORUS MASTER
  '5411677', //ASUS GeForce RTX 3080 10GB ROG STRIX GAMING OC
  '5411910', //MSI GeForce RTX 3080 10GB SUPRIM X
  '5411727', //EVGA GeForce RTX 3080 10GB FTW3
  '5411673', //ASUS GeForce RTX 3080 10GB TUF GAMING
  '5411728', //EVGA GeForce RTX 3080 10GB FTW3 ULTRA
  '5411782', //Gigabyte GeForce RTX 3080 10GB VISION OC
  '5411697', //ASUS GeForce RTX 3080 10GB TUF GAMING OC
  '5411692', //Gigabyte Geforce RTX 3080 10GB GAMING OC
  '5411689', //MSI GeForce RTX 3080 10GB GAMING X TRIO
  '5411724', //EVGA GeForce RTX 3080 10GB XC3 BLACK
  '5411725', //EVGA GeForce RTX 3080 10GB XC3
  '5411726', //EVGA GeForce RTX 3080 10GB XC3 ULTRA
);

$cards = [];

$inetJson = getJsonFromApi('https://www.inet.se/api/products', $inetCardIds);
foreach($inetJson as $key=>$json) {
  $id = $json['id'];
  $loc = $json['qty']['00'];

  $card['id'] = $id;
  $card['productUrl'] = "https://www.inet.se/produkt/". $id ."/". $json['urlName'];
  $card['price'] = $json['price']['price'];
  
  $name = $json['name'];
  $name = str_ireplace("GeForce RTX 3080 10GB", "", $name);
  $name = str_ireplace("  ", " ", $name);
  $card['name'] = $name;
  
  $card['qty'] = getInetCardStock($json);
  $card['status'] = getInetCardStatus($json['qty']['00'], $card['qty']);

  $restockDate = array_key_exists('restockDate', $loc) ? $loc['restockDate'] : null;
  $restockDate = str_ireplace("T00:00:00", "", $restockDate);
  $card['restockDate'] = $restockDate;

  $card['restockDays'] = array_key_exists('restockDays', $loc) ? $loc['restockDays'] : null;

  $card['store']['name'] = "Inet";
  $card['store']['image'] = "https://inetimg2.se/img/logo/inet-logo-rgb-pos-new.svg";

  $cards[$id] = $card;
}
// pp(json_encode($cards));


$prisjaktEndpoint = 'https://www.prisjakt.nu/_internal/graphql?release=2021-05-19T09:22:26Z|87370e89&version=ded13a&main=productCollection&variables={"slug":"grafikkort","type":"c","query":{"url":null,"filters":[{"id":"5530","type":"term","property":"4104"},{"id":"36254","type":"term","property":"532"}],"aggregations":[],"offset":0},"productPropertyIds":["532","6716"],"productPropertyColumnIds":["532","6716"],"campaignId":4,"personalizationClientId":"","pulseEnvironmentId":""}';
$prisjaktRes = getJsonFromApi($prisjaktEndpoint);
$prisjaktJson = $prisjaktRes['data']['productCollection']['slices'][5]['products'];

foreach($prisjaktJson as $key=>$json) {
  $id = $json['id'];

  $card['id'] = $id;
  
  $name = $json['name'];
  $name = str_ireplace("GeForce RTX 3080", "", $name);
  $name = str_ireplace(" 10GB", "", $name);
  $name = str_ireplace(" 3xDP", "", $name);
  $name = str_ireplace(" HDMI", "", $name);
  $name = str_ireplace(" 2xHDMI", "", $name);
  $name = str_ireplace(" 3xHDMI", "", $name);
  $name = str_ireplace(" 10GB", "", $name);
  $name = str_ireplace("  ", " ", $name);
  $card['name'] = $name;
  
  $card['productUrl'] = "https://www.prisjakt.nu". $json['pathName'];
  
  $priceEndpoint = 'https://www.prisjakt.nu/_internal/graphql?release=2021-05-19T09:22:26Z|87370e89&version=ded13a&main=product&variables={"id":'. $id .',"offset":0,"section":"main","marketCode":"se","personalizationExcludeCategories":[],"recommendationsContextId":"product-page","includeSecondary":false,"excludeTypes":["used_product","not_in_mint_condition","not_available_for_purchase"],"variants":null,"advized":true,"priceList":true,"userActions":true,"badges":true,"media":true,"campaign":true,"relatedProducts":true,"campaignDeals":true,"priceHistory":true,"campaignId":4,"personalizationClientId":"","pulseEnvironmentId":""}';
  $res = getJsonFromApi($priceEndpoint);
  $bestNode = getBestPrisjaktPrice($res['data']['product']['prices']['nodes']);

  $card['price'] = $bestNode['price']['inclShipping'] !== null ? $bestNode['price']['inclShipping'] : $bestNode['price']['exclShipping'];
  $card['status'] = getPriskaktCardStatus($bestNode['stock']);

  $qty = $card['status'] == ProductStatus::KnownStock ? $bestNode['stock']['statusText'] : '0';
  $qty = str_ireplace(" st i lager", "", $qty);
  $card['qty'] = (int)$qty;

  $restockDate = $card['status'] == ProductStatus::Incoming ? $bestNode['stock']['statusText'] : '';
  $restockDate = str_ireplace("Kommer ", "", $restockDate);
  $restockDate = str_ireplace("T00:00:00", "", $restockDate);
  $card['restockDate'] = $restockDate;

  $card['restockDays'] = strlen($restockDate) > 0 ? getDaysToDate($restockDate) : '';

  $card['store']['name'] = $bestNode['store']['name'];
  $card['store']['image'] = "https://pricespy-75b8.kxcdn.com/g/rfe/logos/logo_se_v2_light.svg";

  $cards[$id] = $card;
}


function compareCards($a, $b) {
  if ($a['status'] == $b['status']) {
    if ($a['status'] == ProductStatus::Incoming) { return $a['restockDays'] > $b['restockDays'] ? 1 : -1; }
    return $a['price'] > $b['price'] ? 1 : -1;
  }
  return $a['status'] > $b['status'] ? 1 : -1;
}





usort($cards, "compareCards");
//pp($cards);
echo json_encode($cards);





function getJsonFromApi($endpoint, $body = null) {

  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $endpoint);
  curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true); // Will return the response, if false it prints the response
  curl_setopt($handle, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
  ));
  
  if ($body !== null) {
    curl_setopt($handle, CURLOPT_POSTFIELDS, "['". implode("', '", $body) ."']");
  }

  $result = curl_exec($handle);
  curl_close($handle);

  return json_decode($result, true);
}

function getInetCardStock($cardJson) {
  $stock = 0;
  foreach($cardJson['qty'] as $store) { $stock = $stock + $store['qty']; }  
}

function getInetCardStatus($loc, $stock) {
  $status = ProductStatus::SoldOut;
  
  if ($stock > 0) { return ProductStatus::KnownStock; }
  else if ( array_key_exists('restockDate', $loc) ) { return ProductStatus::Incoming; }
  else if ( array_key_exists('isDelayed', $loc) && $loc['isDelayed'] == true) { return ProductStatus::Delayed; }
  else if ( $stock == 0 && array_key_exists('restockDays', $loc) ) { return ProductStatus::Incoming; }
  else if ( array_key_exists('blocked', $loc) && $loc['blocked'] == true) { return ProductStatus::Blocked; }
  return $status;
}



function compareNodes($a, $b) {
  $aStatus = getPriskaktCardStatus($a['stock']);
  $bStatus = getPriskaktCardStatus($b['stock']);
  if ($aStatus == $bStatus) {
    $aPrice = $a['price']['inclShipping'] !== null ? $a['price']['inclShipping'] : $a['price']['exclShipping'];
    $bPrice = $b['price']['inclShipping'] !== null ? $b['price']['inclShipping'] : $b['price']['exclShipping'];
    return $aPrice > $bPrice ? 1 : -1;
  }
  return $aStatus > $bStatus ? 1 : -1;
}

function getBestPrisjaktPrice($priceNodes) {
  usort($priceNodes, "compareNodes");
  return $priceNodes[0];
}


function getPriskaktCardStatus($stock) {
  $string = $stock['status'];
  $text = $stock['statusText'];

  $status = ProductStatus::Na;

  if ($string == 'in_stock') {
    if (str_contains($text, "st")) { return ProductStatus::KnownStock; }
    return ProductStatus::InStock; 
  }
  else if ($string == 'incoming' || str_contains($text, 'Kommer')) { return ProductStatus::Incoming; }
  else if ($string == 'not_in_stock') { return ProductStatus::SoldOut; }
  return $status;
}

function getDaysToDate($target) {
  $now = date("Y/m/d");
  $start = new DateTime($now);
  $end = new DateTime($target);
  if ($start > $end) {return 999;}
  return $end->diff($start)->format("%a");
}

function pp($input) {
  echo '<pre>';
  print_r($input);
  //var_dump($input);
  echo '</pre>';
}


die;

?>