<?php

require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getInetCards() {

  $inetCardIds = array(
    // 3080
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
    // 3080 ti
    '5412319', //ASUS GeForce RTX 3080 Ti 12GB TUF GAMING OC
    '5412336', //MSI GeForce RTX 3080 Ti 12GB SUPRIM X
    '5412337', //MSI GeForce RTX 3080 Ti 12GB GAMING X TRIO
    '5412363', //PNY GeForce RTX 3080 Ti 12GB XLR8 Gaming REVEL RGB
    '5412369', //EVGA GeForce RTX 3080 Ti 12GB XC3 ULTRA
    '5412370', //EVGA GeForce RTX 3080 Ti 12GB XC3 ULTRA
    '5412383', //Gigabyte Geforce RTX 3080 Ti 12GB AORUS MASTER
    '5412320', //ASUS GeForce RTX 3080 Ti 12GB STRIX GAMING OC
  );

  $cards = [];

  $inetJson = getJsonFromApi('https://www.inet.se/api/products', $inetCardIds);
  foreach($inetJson as $key=>$json) {

    $id = $json['id'];
    // $card['id'] = $id;

    $card['url'] = "https://www.inet.se/produkt/". $id ."/". $json['urlName'];
    $card['price'] =  array_key_exists('price', $json) ? $json['price']['price'] : 0;
    
    // skip expired products
    if ($card['price'] == 0) {
      continue;
    }

    $name = $json['name'];
    $card['name'] = cleanCardName($name);
    
    $qty = getInetCardStock($json);
    //$card['qty'] = $qty;
    $card['status'] = getInetCardStatus($json['qty']['00'], $qty);


    $loc = $json['qty']['00'];
    $restockDate = array_key_exists('restockDate', $loc) ? $loc['restockDate'] : '';
    $restockDate = str_ireplace("T00:00:00", "", $restockDate);
    $card['restockDate'] = strtotime($restockDate) > strtotime('now') ? $restockDate : '';

    // do a sanity check on restock date
    if ( $card['status'] == ProductStatus::Incoming && strtotime($restockDate) <= strtotime('now') ) {
      $card['status'] = ProductStatus::Delayed;
      $card['restockDays'] = '';
    } else { 
      $card['restockDays'] = strlen($restockDate) > 0 ? getDaysToDate($restockDate) : '';
    }

    $card['restockDays'] = $card['restockDays'] == 0 ? '' : $card['restockDays'];

    $card['source'] = "inet";

    // $card['store']['name'] = "Inet";
    // $card['store']['image'] = "https://inetimg2.se/img/logo/inet-logo-rgb-pos-new.svg";
    
    $cards[$id] = $card;
  }

  // pp(json_encode($cards));
  return $cards;

}


function getInetCardStock($cardJson) {
  $stock = 0;
  foreach($cardJson['qty'] as $store) { $stock = $stock + $store['qty']; }
  return $stock;
}



function getInetCardStatus($loc, $stock) {
  if ($stock > 0) { return ProductStatus::KnownStock; }
  else if ( array_key_exists('restockDate', $loc) ) { return ProductStatus::Incoming; }
  else if ( array_key_exists('isDelayed', $loc) && $loc['isDelayed'] == true) { return ProductStatus::Delayed; }
  else if ( $stock == 0 && array_key_exists('restockDays', $loc) ) { return ProductStatus::Incoming; }
  else if ( array_key_exists('blocked', $loc) && $loc['blocked'] == true) { return ProductStatus::Blocked; }
  return ProductStatus::SoldOut;
}



?>