<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getCompumailCards() {

  $blacklist = [];
  $cards = [];

  $productList = file_get_html("https://compu-mail.se/en/category/hardware/komponenterogtilbehor/grafikkort/nvidia?page=1&tb%5Blimit%5D=96&tb%5Bview%5D=line&tb%5Bsort%5D=price_asc&tb%5Bstock%5D=instock&filters={%22A00247%22:[%22NVIDIA%20GeForce%20RTX%203080%22,%22NVIDIA%20GeForce%20RTX%203080%20Ti%22]}");
  
  // build a list of prodict IDs from the search result page
  $cardIds = [];
  foreach($productList->find('div.product-line') as $listItem) {
    $a = $listItem->find('h2 a.product-link', 0);
    $id = $a->getAttribute('data-product-id');
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    } else {
      array_push($cardIds, $id);
    }
  }

  // make a separate request for status of cards
  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, 'https://compu-mail.se/en/stockstatus');
  curl_setopt($handle, CURLOPT_POST, true);
  curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_REFERER, 'https://compu-mail.se');
  curl_setopt($handle, CURLOPT_HTTPHEADER, array(
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'origin: https://compu-mail.se',
    'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36',
    'x-requested-with: XMLHttpRequest',
  ));
  $postField = getCompuPostString($cardIds);
  curl_setopt($handle, CURLOPT_POSTFIELDS, $postField);

  $result = curl_exec($handle);
  curl_close($handle);
  $statusById = json_decode($result, true);

  // build a list of cards
  foreach($productList->find('div.product-line') as $listItem) {

    $a = $listItem->find('h2 a.product-link', 0);
    
    $id = $a->getAttribute('data-product-id');
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    // Create a DOM object
    $htmlString = $statusById[$id];
    $statusHtml = new simple_html_dom();
    $statusHtml->load($htmlString);

    $inStock = $statusHtml->find('.inventory-green', 0);
    $incoming = $statusHtml->find('.inventory-yellow', 0);
    if (is_object($inStock)) {
      $card['status'] = ProductStatus::InStock;
      $card['restockDate'] = '';
      $card['restockDays'] = '';
    } else if (is_object($incoming)) {
      $span = $statusHtml->find('ul.stockStatusOnProdPage span', 0);
      if (is_object($span)) {
        $card['status'] = ProductStatus::Incoming;
        $date = $span->getAttribute('data-delivery-date');
        $fixedDate = date("Y-m-d", strtotime($date));
        $card['restockDate'] = $fixedDate;
        $card['restockDays'] = getDaysToDate($fixedDate);
      } else {
        continue;
      }
    } else {
      continue;
    }

    $href = $a->href;
    $card['url'] = "https://compu-mail.se". $href;

    $name = $a->getAttribute('data-product-name');
    $card['name'] = cleanCardName($name);

    $price = $listItem->find('span.price',0)->getAttribute('data-price');
    if (!is_object($price)) { continue; }
    $card['price'] = (int) $price;
    
    $card['source'] = "compumail";

    $cards[$id] = $card;
  }

  return $cards;
}


function getCompuPostString($cardIds) {
  $string = '';
  $lastIndex = sizeof($cardIds);
  foreach ($cardIds as $id) {
    $string = $string . 'products%5B%5D=' . $id . '&';
  }
  return $string;
}