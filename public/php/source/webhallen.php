<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';


function getWebhallenCards() {

  $blacklist = [];

  $cards = [];
  
  $webhallenJson = getJsonFromApi('https://www.webhallen.com/api/search/product?query%5BsortBy%5D=searchRating&query%5Bfilters%5D%5B0%5D%5Btype%5D=searchString&query%5Bfilters%5D%5B0%5D%5Bvalue%5D=rtx+3080&query%5Bfilters%5D%5B1%5D%5Btype%5D=stock&query%5Bfilters%5D%5B1%5D%5Bvalue%5D=1&query%5Bfilters%5D%5B2%5D%5Btype%5D=category&query%5Bfilters%5D%5B2%5D%5Bvalue%5D=47&query%5BminPrice%5D=0&query%5BmaxPrice%5D=999999&page=1');
  foreach($webhallenJson['products'] as $key=>$json) {

    $id = $json['id'];
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $stock = $json['stock']['web'];
    if (!isset($stock) || !($stock > 0) ) {
      continue;
    }

    $card['status'] = ProductStatus::InStock;

    $name = $json['name'];
    $card['name'] = cleanCardName($name);
    $card['price'] = (int) $json['price']['price'];
    $card['url'] = "https://www.webhallen.com/se/product/". $id;
    
    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = "webhallen";

    $cards[$id] = $card;
  }

  return $cards;
}