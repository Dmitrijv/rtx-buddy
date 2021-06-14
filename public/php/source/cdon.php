<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getCdonCards() {

  $blacklist = [];
  $blacklist[71786767] = true;

  $cards = [];

  $html = file_get_html("https://cdon.se/catalog/search?q=rtx+3080&taxonomyId=392"); // ->save()

  foreach($html->find('div.catalog-page--product-list-wrapper a.p-c') as $a) {
    $id = $a->getAttribute('data-id');

    // skip blacklisted products
    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $name = $a->getAttribute('data-product-name');
    $card['name'] = cleanCardName($name);
    
    $card['url'] = "https://cdon.se" . $a->href;
    
    $card['price'] = $a->getAttribute('data-product-price');
    $card['status'] = $a->getAttribute('data-p-state') == "Buyable" ? ProductStatus::InStock : ProductStatus::Na;

    $card['restockDays'] = '';
    $card['restockDate'] = '';
    
    $card['source'] = 'cdon';

    $cards[$id] = $card;
  }

  return $cards;
}