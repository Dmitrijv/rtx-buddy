<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';


function getRdebutikCards() {

  $blacklist = [];
  $blacklist['174290'] = true;
  $blacklist['174278'] = true;

  $cards = [];

  $html = file_get_html("https://rdebutik.se/search_result/se/word/rtx+3080/page/1");

  foreach($html->find('div.product_box_div') as $listItem) {
    
    $a = $listItem->find('a', 1);

    preg_match('/\/417\/(.*)\/sort/mU', $a, $matches);
    $id = $matches[1];

    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $card['id'] = $id;
    
    $url = $a->href;
    if (!isset($url)) {
      continue;
    }

    $card['url'] = "https://rdebutik.se/" . $url;

    $name = $a->getAttribute('title');
    $name = cleanCardName($name);
    if (strlen($name) == 0) {
      $name = "Generic";
    }
    $card['name'] = $name;

    $statusEndpoint = 'https://rdebutik.se/o_ajax_get_product_availability_couriers_list.php?product_id='.$id.'&lang=se';

    $status = mimicAjax($statusEndpoint);
    $status = $status['couriers_tab_content'];
    $card['status'] = str_contains($status, 'Beställ nu') ? ProductStatus::InStock : ProductStatus::SoldOut;

    // Skip cards that are not in stock or have an incoming date
    if ($card['status'] > ProductStatus::Incoming) {
      continue;
    }

    $price = $listItem->find('.product_price_wo_discount_listing', 0)->plaintext;

    preg_match_all('/(\d+)\.00/m', $price, $matches, PREG_SET_ORDER, 0);
    $card['price'] = (int) $matches[0][1];

    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = 'rdebutik';

    $cards[$id] = $card;
  }

  return $cards;

}