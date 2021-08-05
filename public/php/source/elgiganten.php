<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getElgigantenCards() {

  $blacklist = [];
  $blacklist[328524] = true;
  $blacklist[313946] = true;
  $blacklist[329986] = true;
  $blacklist[313949] = true;

  $cards = [];
  
  $context = stream_context_create(
    array(
      "http" => array(
        'method'=>"GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.106 Safari/537.36\r\n" .
                    "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n" .
                    "accept-language: en-US,en;q=0.9\r\n" .
                    "accept-encoding: compress,\r\n" .
                    "sec-fetch-dest: document\r\n"
                    )
        )
  );

  $searchUrl = 'https://www.elgiganten.se/search?SearchParameter=%26%40QueryTerm%3Drtx%2B3080%26bitem%3D0%26CategoryNameLevel2%3DDatorer%2B%2526%2BTillbeh%25C3%25B6r%2B__%2BDatorkomponenter%26online%3D1%26%40Sort.ProductListPrice%3D0%26%40Sort.name%3D0&PageSize=12&ProductElementCount=13&ContentElementCount=0&StoreElementCount=0&searchResultTab=Products&SearchTerm=rtx+3080&NumberRanges=';

  $html = false;
  try {
    $html = file_get_html($searchUrl, false, $context);
    if($html === false) { return []; }
  } catch (Exception $e) {
    return [];
  }

  foreach($html->find('div.col-mini-product') as $listItem) {

    $a = $listItem->find('a.product-image-link', 0);

    $url = $a->href;

    preg_match('/\/grafikkort\/(.*)\//mU', $url, $matches);
    if (array_key_exists(1, $matches) == false) {
      continue;
    }

    $id = $matches[1];    

    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }
    
    $inStockIcon = $listItem->find('span.checkout-spacing', 0);
    if (!is_object($inStockIcon)) {
      continue;
    }

    $card['status'] = ProductStatus::InStock;

    $name = $a->title;
    $card['name'] = cleanCardName($name);

    $price = $listItem->find('div.product-price',0)->plaintext;
    preg_match_all('/([0-9]+)/m', $price, $matches, PREG_SET_ORDER, 0);
    $card['price'] = (int) $matches[0][0] . $matches[1][0];

    $card['url'] = $url;
    
    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = "elgiganten";

    $cards[$id] = $card;
  }

  return $cards;
}