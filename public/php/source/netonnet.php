<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getNetonnetCards() {

  $blacklist = [];
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

  $listUrl = "https://www.netonnet.se/search?query=RTX%203080&page=1&pageSize=48&filter=170666260";
  $content = file_get_html($listUrl, false, $context);

  foreach($content->find('div.cProductItem div.panel-body') as $listItem) {

    $id = $listItem->find('div.smallHeader div.shortText a', 0)->getAttribute('data-track');

    $name = $listItem->find('div.smallHeader div.shortText a', 0)->getAttribute('title');
    if (str_contains($name, "3090") === true || str_contains($name, "3070") === true) {
      continue;
    }

    $name = cleanCardName($name);
    $card['name'] = trim($name);

    $card['url'] = "https://www.netonnet.se" . $listItem->find('div.smallHeader div.shortText a', 0)->getAttribute('href');

    $price = $listItem->find('div.priceContainer span.price', 0)->innertext; // 19&nbsp;990:-
    $price = html_entity_decode($price);
    $price = str_replace("\xA0", '', $price);
    $price = preg_replace("/[^0-9]/", "", $price);
    $card['price'] = intval($price);

    $statusDiv =  $listItem->find('div.warehouseStockStatusContainer', 0);
    $card['status'] = getNetonnetStatus($statusDiv);

    // do a sanity check on restock date
    if ( $card['status'] == ProductStatus::Incoming ) {
      $productPage = file_get_html($card['url'], false, $context);
      $restockDate = $productPage->find('div.deliveryInfoText span', 0)->plaintext;
      $restockDate = str_ireplace("Förväntat datum: ", "", $restockDate);
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

    $card['source'] = 'netonnet';

    $cards[$id] = $card;
  }

  return $cards;
}

function getNetonnetStatus($status) {
  $inStockIcons = $status->find('span.stockStatusInStock');
  if ( count($inStockIcons) > 0 ) { return ProductStatus::InStock; }
  $preorderIcons = $status->find('span.stockStatusPreOrder');
  if ( count($preorderIcons) > 0 ) { return ProductStatus::Incoming; }
  return ProductStatus::SoldOut;
}