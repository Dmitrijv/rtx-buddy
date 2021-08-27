<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';


function getDatagrottanCards() {

  $blacklist = [];
  $blacklist['1001573951'] = true;
  $blacklist['1002025921'] = true; // ASUS ROG GC31S-030- ROG Flow X13 Docking Station RTX 3080

  $cards = [];

  $html = file_get_html("https://www.datagrottan.se/search/ps_90?ffd=l-c-1000051_36132&kw=rtx+3080");
  if($html === FALSE) { return []; }

  foreach($html->find('div.b-product-list div.b-product-list__item') as $listItem) {

    $id = $listItem->find('a.b-product-list__item-name', 0)->getAttribute('data-product-link');
    // skip blacklisted products
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    // skip if card is not in stock
    $inStockIcon = $listItem->find('.icon-in-stock', 0);
    if (!is_object($inStockIcon)) {
      continue;
    }

    $name = $listItem->find('a.b-product-list__item-name', 0)->plaintext;
    $name = cleanCardName($name);
    $card['name'] = trim($name);

    $card['url'] = "https://datagrottan.se" . $listItem->find('a.b-product-list__item-name', 0)->href;

    $price = $listItem->find('.b-price_partslist', 0);
    if (!is_object($price)) {
      continue;
    }

    $price = $price->plaintext;
    //echo $price;
    $price = str_ireplace(".", "", $price);
    $price = str_ireplace(":-", "", $price);
    $price = intval($price);
    $card['price'] = $price;

    $card['status'] = ProductStatus::InStock;

    $card['restockDate'] = '';
    $card['restockDays'] = '';

    $card['source'] = 'datagrottan';

    $cards[$id] = $card;
  }

  return $cards;
}