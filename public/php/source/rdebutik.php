<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';


function getRdebutikCards() {

  $blacklist = [];
  // $blacklist['3200210610'] = true;

  $cards = [];

  $html = file_get_html("https://rdebutik.se/search_result/se/word/rtx+3080/page/1");

  foreach($html->find('div.product_box_div a') as $listItem) {
    
    $a = $listItem->find('a', 0);

    preg_match('/\/417\/(.*)\/sort/mU', $a, $matches);
    $id = $matches[1];

    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $card['id'] = $id;
    
    $url = $url->href;
    $card['url'] = "https://rdebutik.se/" . $url;

    $name = $a->plaintext;
    $card['name'] = cleanCardName($name);

    $price = $listItem->find('.product_price_wo_discount_listing', 0)->plaintext;
    $price = str_ireplace(" ", "", $price);
    $price = str_ireplace("&nbsp;", "", $price);
    $price = str_ireplace("kr", "", $price);
    $price = str_ireplace(".00", "", $price);
    $card['price'] = (int) trim($price);    

    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = 'pricerunner';

    $cards[$id] = $card;
  }

  return $cards;

}