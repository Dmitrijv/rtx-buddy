<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getKomplettCards() {

  $blacklist = [];
  $blacklist[1187638] = true;

  $cards = [];
  
  $html = file_get_html("https://www.komplett.se/search?q=rtx%203080&nlevel=10000%C2%A728003%C2%A710412&sort=PriceDesc%3ADESCENDING&stockStatus=InStock");
  
  foreach($html->find('div.product-list-item') as $listItem) {

    $a = $listItem->find('a.product-link', 0);

    $url = $a->href;

    preg_match('/\/product\/(.*)\//mU', $url, $matches);
    $id = $matches[1];

    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }
    
    $inStockIcon = $listItem->find('.stockstatus-instock', 0);
    if (!is_object($inStockIcon)) {
      continue;
    }

    $card['status'] = ProductStatus::InStock;

    $name = $a->title;
    $card['name'] = cleanCardName($name);

    $price = $listItem->find('span.product-price-now',0)->plaintext;
    preg_match_all('/([0-9]+)/m', $price, $matches, PREG_SET_ORDER, 0);
    $card['price'] = (int) $matches[0][1] . $matches[2][1];


    $card['url'] = "https://www.komplett.se". $url;
    
    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = "komplett";

    $cards[$id] = $card;
  }

  return $cards;
}