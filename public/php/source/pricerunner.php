<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';


function getPricerunnerCards() {

  $blacklist = [];
  $blacklist['37-3200210610'] = true;

  $cards = [];

  $html = file_get_html("https://cdon.se/catalog/search?q=rtx+3080&taxonomyId=392");

  foreach($html->find('div._3808JSp2qk div._2Vdwcz_zWR') as $listItem) {

    $url = $listItem->find('a', 0)->href;
    preg_match('/\/pl\/(.*)\/Grafikkort/mU', $url, $matches);
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