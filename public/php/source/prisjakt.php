<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getPrisjaktCards() {

  $cards = [];

  // Fetch current GraphQL version from the landing page.
  $prisjaktHtml = file_get_html('https://www.prisjakt.nu/')->save();
  $match = preg_match('/window\.RELEASE = "(.+)";.+window\.GRAPHQL_VERSION = "(.+)";/mU', $prisjaktHtml, $matches);
  if ($match !== 1) { 
    echo 'Could not fetch latest GraphQL version.';
    die;
  }

  $GQLRelease = $matches[1];
  $GQLVersion = $matches[2];

  $prisjaktEndpoint = 'https://www.prisjakt.nu/_internal/graphql?release='.$GQLRelease.'&version='.$GQLVersion.'&main=search&variables={%22id%22:%22search%22,%22query%22:%22rtx%203080%22,%22sort%22:%22score%22,%22order%22:%22desc%22,%22offset%22:0,%22filters%22:[{%22id%22:%22category_id%22,%22selected%22:[]},{%22id%22:%22brand_id%22,%22selected%22:[]},{%22id%22:%22lowest_price%22},{%22id%22:%22user_rating%22}],%22productModes%22:[%22product%22,%22raw%22],%22campaignId%22:4,%22personalizationClientId%22:%22%22,%22pulseEnvironmentId%22:%22%22}';
  $prisjaktRes = getJsonFromApi($prisjaktEndpoint);
  $prisjaktJson = $prisjaktRes['data']['productCollection']['slices'][5]['products'];

  //create the array of cURL handles and add to a multi_curl
  $mh = curl_multi_init();
  foreach($prisjaktJson as $key=>$json) {
    $id = $json['id'];
    $url = 'https://www.prisjakt.nu/_internal/graphql?release='.$GQLRelease.'&version='.$GQLVersion.'&main=product&variables={"id":'.$id.',"offset":0,"section":"main","marketCode":"se","personalizationExcludeCategories":[],"recommendationsContextId":"product-page","includeSecondary":false,"excludeTypes":["used_product","not_in_mint_condition","not_available_for_purchase"],"variants":null,"advized":true,"priceList":true,"userActions":true,"badges":true,"media":true,"campaign":true,"relatedProducts":true,"campaignDeals":true,"priceHistory":true,"campaignId":4,"personalizationClientId":"","pulseEnvironmentId":""}';

    $chs[$id] = curl_init();
    curl_setopt($chs[$id], CURLOPT_URL, $url);
    curl_setopt($chs[$id], CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($chs[$id], CURLOPT_RETURNTRANSFER, true); // Will return the response, if false it prints the response
    curl_setopt($chs[$id], CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
    ));

    curl_multi_add_handle($mh, $chs[$id]);
  }

  // run all requests together
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while ($running);

  // save the responses
  $prisjaktPriceNodeById = [];
  foreach(array_keys($chs) as $key) {
    $response = curl_multi_getcontent($chs[$key]);
    $response = json_decode($response, true);
    if (empty($error)) {
      $prisjaktPriceNodeById[$key] = $response['data']['product']['prices']['nodes'];
    }
    else {
      // die;
      echo "The request $key return a error: $error" . "</br>";
    }
    curl_multi_remove_handle($mh, $chs[$key]);
  }

  // close current handler
  curl_multi_close($mh);

  // build card list
  foreach($prisjaktJson as $key=>$json) {
    $id = $json['id'];

    // $card['id'] = $id;
    
    $card['name'] = cleanCardName($json['name']);
    
    $card['url'] = "https://www.prisjakt.nu". $json['pathName'];
    
    $bestNode = getBestPrisjaktNode($prisjaktPriceNodeById[$id]);

    // skip expired products
    if ($bestNode == null) {
      continue;
    }

    $card['price'] = $bestNode['price']['inclShipping'] !== null ? $bestNode['price']['inclShipping'] : $bestNode['price']['exclShipping'];
    $card['status'] = getPriskaktCardStatus($bestNode['stock']);

    $restockDate = $card['status'] == ProductStatus::Incoming ? $bestNode['stock']['statusText'] : '';
    $restockDate = str_ireplace("Kommer ", "", $restockDate);
    $restockDate = str_ireplace("T00:00:00", "", $restockDate);
    $card['restockDate'] = $restockDate;


    // $qty = $card['status'] == ProductStatus::KnownStock ? $bestNode['stock']['statusText'] : '0';
    // $qty = str_ireplace(" st i lager", "", $qty);
    // $card['qty'] = (int)$qty;

    // do a sanity check on restock date
    if ( $card['status'] == ProductStatus::Incoming && strtotime($restockDate) <= strtotime('now') ) {
      $card['status'] = ProductStatus::Delayed;
      $card['restockDays'] = '';
    } else {
      $card['restockDays'] = strlen($restockDate) > 0 ? getDaysToDate($restockDate) : '';
    }

    $card['source'] = "prisjakt";
    // $card['store']['name'] = $bestNode['store']['name'];
    // $card['store']['image'] = "https://pricespy-75b8.kxcdn.com/g/rfe/logos/logo_se_v2_light.svg";

    $cards[$id] = $card;
  }

  return $cards;
}




function comparePrisjaktNodes($a, $b) {
  $aStatus = getPriskaktCardStatus($a['stock']);
  $bStatus = getPriskaktCardStatus($b['stock']);
  if ($aStatus == $bStatus || ($aStatus + $bStatus) <= 2) {
    $aPrice = $a['price']['inclShipping'] !== null ? $a['price']['inclShipping'] : $a['price']['exclShipping'];
    $bPrice = $b['price']['inclShipping'] !== null ? $b['price']['inclShipping'] : $b['price']['exclShipping'];
    return $aPrice > $bPrice ? 1 : -1;
  }
  return $aStatus > $bStatus ? 1 : -1;
}



function getBestPrisjaktNode($priceNodes) {
  if ( is_array($priceNodes) && count($priceNodes) > 0 ) {
    usort($priceNodes, "comparePrisjaktNodes");
    return $priceNodes[0];
  }
  return null;
}



function getPriskaktCardStatus($stock) {
  $string = $stock['status'];
  $text = $stock['statusText'];

  if ($string == 'in_stock') {
    if (str_contains($text, "st")) { return ProductStatus::KnownStock; }
    return ProductStatus::InStock; 
  }
  else if ($string == 'incoming' || str_contains($text, 'Kommer')) { return ProductStatus::Incoming; }
  else if ($string == 'not_in_stock') { return ProductStatus::SoldOut; }
  return ProductStatus::Na;
}


?>