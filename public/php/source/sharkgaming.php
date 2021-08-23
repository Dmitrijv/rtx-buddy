<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getSharkgamingCards() {

  $blacklist = [];
  $blacklist['https://sharkgaming.se/msi-rtx-3080-sea-hawk-x-10g-lhr'] = true;
  $blacklist['https://sharkgaming.se/great-white-shark'] = true;
  $blacklist['https://sharkgaming.se/mighty-deep-blue-gaming-pc'] = true;
  $blacklist['https://sharkgaming.se/white-shark-megalodon'] = true;
  $blacklist['https://sharkgaming.se/shark-deep-blue'] = true;
  $blacklist['https://sharkgaming.se/mighty-shark-bloodlust'] = true;
  $blacklist['https://sharkgaming.se/shark-deep-blue-amd'] = true;
  $blacklist['https://sharkgaming.se/shark-deep-blue-amd'] = true;
  $blacklist['https://sharkgaming.se/shark-gaming-esport-edition'] = true;
  $blacklist['https://sharkgaming.se/almighty-shark'] = true;
  $blacklist['https://sharkgaming.se/shark-bloodlust'] = true;
  $blacklist['https://sharkgaming.se/max-bite-brutality'] = true;
  $blacklist['https://sharkgaming.se/max-bite-madness-3518'] = true;
  $blacklist['https://sharkgaming.se/white-shark-massacre-gaming-pc'] = true;
  $blacklist['https://sharkgaming.se/max-bite-predator-v2'] = true;
  $blacklist['https://sharkgaming.se/max-bite-bloodlust'] = true;
  $blacklist['https://sharkgaming.se/max-bite-ultimator'] = true;
  $blacklist['https://sharkgaming.se/hypenade-edition'] = true;
  $blacklist['https://sharkgaming.se/shark-gaming-heaton-edition'] = true;
  $blacklist['https://sharkgaming.se/shark-brutality'] = true;
  $blacklist['https://sharkgaming.se/max-bite-extreme-v2'] = true;

  $cards = [];

  $html = file_get_html("https://sharkgaming.se/catalogsearch/result?q=rtx+3080");

  foreach($html->find('ul.products-grid li.item') as $listItem) {

    $inStockIcon = $listItem->find('p.in-stock', 0);
    if (!is_object($inStockIcon)) {
      continue;
    }

    $a = $listItem->find('h2.product-name a', 0);

    $id = $a->href;
    $url = $a->href;

    if (array_key_exists($id, $blacklist) == true) {
      continue;
    }

    $card['status'] = ProductStatus::InStock;

    $name = $a->title;
    $card['name'] = cleanCardName($name);

    $price = $listItem->find('span.price', 0)->plaintext;
    $price = str_ireplace(" kr.", "", $price);
    $card['price'] = (int) $price;

    $card['url'] = $url;
    
    $card['restockDays'] = '';
    $card['restockDate'] = '';

    $card['source'] = "sharkgaming";

    $cards[$id] = $card;
  }

  return $cards;




}