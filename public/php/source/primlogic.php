
<?php

require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getPrimlogicCards() {

  $blacklist = [];
  $blacklist[10145] = true;
  $blacklist[10601] = true;
  $blacklist[10600] = true;
  $blacklist[10146] = true;
  $blacklist[10141] = true;

  $cards = [];

  $endpoint = "https://api.primlogic.se/products?search=rtx%203080&cat_ids=630&in_stock=0&page=1&limit=240&sort_by=Leveransdatum&descending=0&get_score=1&get_product_ids=1";
  $res = getJsonFromApi($endpoint);
  if ( !is_object($res) && !is_array($res) ) { return []; }
  
  $cardJson = $res['docs'];
  
  foreach($cardJson as $key=>$json) {

    $id = $json['Produktnr'];

    // skip blacklisted products
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $name = $json['Produktnamn'];
    $card['name'] = cleanCardName($name);

    $card['url'] = "https://www.primlogic.se/produkter/". $id;

    $card['status'] = getPrimlogicCardStatus($json);

    // Skip cards that are not in stock or have an incoming date
    if ($card['status'] > ProductStatus::Incoming) {
      continue;
    }

    $card['price'] = $json['Realutpris'];

    $restockDate = $card['status'] == ProductStatus::Incoming ? $json['Leveransdatum'] : '';
    $restockDate = str_ireplace("T00:00:00.000Z", "", $restockDate);
    $card['restockDate'] = $restockDate;

    // do a sanity check on restock date
    if ( $card['status'] == ProductStatus::Incoming && strtotime($restockDate) <= strtotime('now') ) {
      $card['status'] = ProductStatus::Delayed;
      $card['restockDays'] = '';
    } else {
      $card['restockDays'] = strlen($restockDate) > 0 ? getDaysToDate($restockDate) : '';
    }

    $card['source'] = "primlogic";

    $cards[$id] = $card;

  }

  return $cards;
}


function getPrimlogicCardStatus($json) {
  if ($json['Antal_pa_lager'] !== null && $json['Antal_pa_lager'] > 0) {
    return ProductStatus::InStock;
  } else if ($json['Leveransdatum'] !== null) {
    return ProductStatus::Incoming;
  }
  return ProductStatus::Na;
}

?>

