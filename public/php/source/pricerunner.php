<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';


function getPricerunnerCards() {

  $blacklist = [];
  $blacklist['37-3200210610'] = true;
  // $blacklist['37-3200221870'] = true;

  $cards = [];

  $html = file_get_html("https://www.pricerunner.se/cl/37/Grafikkort?attr_60534005=60534009&attr_60534931=100000354,60534998&onlyInStock=true&sort=price_asc");

  foreach($html->find('div._2LGIE9LjOC div._1bgVr-M90D') as $listItem) {

    $url = $listItem->find('a', 0);
    if (!isset($url)) { 
      // pp($url);
      continue;
     }

    $url = $url->href;
    if (!isset($url)) { 
      // pp($url);
      continue;
     }

    preg_match('/\/pl\/(.*)\/Grafikkort/mU', $url, $matches);
    if (!isset($matches[1])) { 
      // pp($matches);
      continue;
     }

    $id = $matches[1];

    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $card['id'] = $id;
    $card['url'] = "https://www.pricerunner.se" . $url;

    $name = $listItem->find('h3._3EQkAqQ5yG', 0)->plaintext;
    $card['name'] = cleanCardName($name);

    $price = $listItem->find('span._1hXG0xPrK5', 0)->plaintext;
    $price = str_ireplace(" ", "", $price);
    $card['price'] = (int) trim($price);

    $card['status'] = ProductStatus::InStock;

    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = 'pricerunner';

    $cards[$id] = $card;
  }

  return $cards;
}