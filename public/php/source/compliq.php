<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getCompliqCards() {

  $blacklist = [];

  $cards = [];

  $html = file_get_html("https://shop.compliq.se/search/l_en/ps_90/s_14?ffd=l-c-100095_36132l-p25394-v2189995&kw=rtx+3080");

  foreach($html->find('div.b-product-list div.b-product-list__item') as $listItem) {

    $id = $listItem->find('a.b-product-list__item-name', 0)->getAttribute('data-product-link');
    // skip blacklisted products
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $name = $listItem->find('a.b-product-list__item-name', 0)->plaintext;
    $name = str_ireplace(" GDDR5,", "", $name);
    $name = str_ireplace(" GDDR6,", "", $name);
    $name = cleanCardName($name);
    $card['name'] = trim($name);

    $card['url'] = "https://shop.compliq.se" . $listItem->find('a.b-product-list__item-name', 0)->href;

    $price = $listItem->find('div.b-price_small', 0)->plaintext;
    //echo $price;

    $price = str_ireplace(".", "", $price);
    $price = str_ireplace(":-", "", $price);
    $price = str_ireplace("inkl. moms", "", $price);
    $price = str_ireplace("incl sales tax", "", $price);
    $price = intval($price);
    $card['price'] = $price;

    $status = $listItem->find('span.b-show-stock__value', 1)->plaintext;
    $card['status'] = getCompliqStatus($status);

    // do a sanity check on restock date
    if ( $card['status'] == ProductStatus::Incoming ) {

      $restockDate = trim($listItem->find('span.b-show-stock__eta-date', 0)->plaintext);

      if ( strlen($restockDate) > 0 && strtotime($restockDate) > strtotime('now') ) {
        $card['restockDate'] = $restockDate;
        $card['restockDays'] = getDaysToDate($restockDate);
      } else {
        $card['status'] = ProductStatus::Delayed;
        $card['restockDays'] = '';
      }

    } else {
      $card['restockDate'] = '';
      $card['restockDays'] = '';
    }

    $card['source'] = 'compliq';

    $cards[$id] = $card;
  }

  return $cards;
}

function getCompliqStatus($string) {
  //echo $string;
  if (str_contains($string, 'Inte p')) { return ProductStatus::SoldOut; }
  if (str_contains($string, '-')) { return ProductStatus::Incoming; }
  if (str_contains($string, ' lager')) { return ProductStatus::InStock; }
  return ProductStatus::Na;
}