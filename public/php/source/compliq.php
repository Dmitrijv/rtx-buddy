<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getCompliqCards() {

  $blacklist = [];
  $blacklist[1001573951] = true;// GIGABYTE AORUS GAMING BOX
  
  $cards = [];

  // $html = file_get_html("https://shop.compliq.se/search/l_en/ps_90?ffd=l-c-100095_36132l-p25394-v2189995&kw=rtx+3080");
  $html = file_get_html("https://shop.compliq.se/search/l_en/ps_90?ffd=l-c-100095_36132&kw=rtx+3080");
  if($html === FALSE) { return []; }

  foreach($html->find('div.b-product-list div.b-product-list__item') as $listItem) {

    $id = $listItem->find('a.b-product-list__item-name', 0)->getAttribute('data-product-link');
    // skip blacklisted products
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $name = $listItem->find('a.b-product-list__item-name', 0)->plaintext;
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


    $inStockIcon = $listItem->find('.icon-in-stock', 0);
    if (is_object($inStockIcon)) {
      $card['status'] = ProductStatus::InStock;
    } else {
      $status = $listItem->find('span.b-show-stock__value', 1)->plaintext;
      $card['status'] = getCompliqStatus($status);
    }

    //$eta = $listItem->find('span.b-show-stock__out-of-stock-with-eta span.b-show-stock__eta-date', 1)->plaintext;
    
    $restockTag = $listItem->find('span.b-show-stock__eta-date', 0);
    if (is_object($restockTag)) {
      $card['status'] == ProductStatus::Incoming;
    }

    // Skip cards that are not in stock or have an incoming date
    if ($card['status'] > ProductStatus::Incoming) {
      continue;
    }

    // do a sanity check on restock date
    if ( $card['status'] == ProductStatus::Incoming ) {
      
      if (!isset($restockTag)) {
        $card['status'] = ProductStatus::Delayed;
        $card['restockDays'] = '';
      } else {

        $restockDate = trim($restockTag->plaintext);
        if ( strlen($restockDate) > 0 && strtotime($restockDate) > strtotime('now') ) {
          $card['restockDate'] = $restockDate;
          $card['restockDays'] = getDaysToDate($restockDate);
        } else {
          $card['status'] = ProductStatus::Delayed;
          $card['restockDays'] = '';
        }

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
  // echo $string;
  // pp($string);
  if (str_contains($string, 'Inte p')) { return ProductStatus::SoldOut; }
  if (str_contains($string, '202')) { return ProductStatus::Incoming; }
  if (preg_match('/\\d/', $string) > 0) { return ProductStatus::InStock; }
  return ProductStatus::Na;
}