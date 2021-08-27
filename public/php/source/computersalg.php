<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';
require_once __DIR__ . '/../lib/multi_curl.php';

function getComputersalgCards() {

  $urls = [
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=1',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=2',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=3',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=4',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=5',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=6',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=7',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=8',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=9',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=10',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=11',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=12',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=13',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=14',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=15',
    // 'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=16',
  ];

  $pages = getPages($urls);

  $blacklist = [];
  $blacklist[7371884] = true; // 2080 Ti
  $blacklist[7000620] = true; // Gamebox
  $blacklist[7556809] = true; // Gamebox
  $blacklist[7576916] = true; // Gamebox
  $blacklist[7353786] = true; // 3060
  $blacklist[7438330] = true; // 3060
  $blacklist[7438335] = true; // 3060
  $blacklist[7353791] = true; // 3060
  $blacklist[7353798] = true; // 3060
  $blacklist[7353792] = true; // 3060 Ti
  $blacklist[7376745] = true; // 3060 Ti
  $blacklist[7576979] = true; // 3060 Ti
  $blacklist[7353800] = true; // 3070
  $blacklist[7373227] = true; // 3070 Ti
  $blacklist[7383417] = true; // 3070 Ti
  $blacklist[7384457] = true; // 3070 Ti
  $blacklist[7383056] = true; // 3070 Ti
  $blacklist[7384456] = true; // 3070 Ti
  $blacklist[7466819] = true; // 3080 Waterforce
  $blacklist[7461632] = true; // 3080 Waterforce
  $blacklist[7261641] = true; // 3080 Waterforce
  $blacklist[7466818] = true; // 3080 Waterforce
  $blacklist[6813126] = true; // 3080 Waterforce
  $blacklist[7373217] = true; // 3080 Hybrid
  $blacklist[7471344] = true; // 3080 Sea Hawk
  $blacklist[7341368] = true; // 3090

  $cards = [];

  foreach ($pages as $string) {
  
    $html = new simple_html_dom();
    $html->load($string);

    // build a list of cards
    foreach($html->find('ul.productlist li.productlist-item') as $index=>$listItem) {

      $a = $listItem->find('a.productNameLink', 0);
      if (!is_object($a)) {
          continue;
      }

      $id = $a->getAttribute('data-toitemid');
      if (array_key_exists($id, $blacklist) == true || array_key_exists($id, $cards) == true) {
        continue;
      }

      $green = $listItem->find('span.green', 0);
      if (!is_object($green)) {
        continue;
      }

      $name = $a->innertext;
      if (
        str_contains($name, '2060')
        || str_contains($name, '3060')
        || str_contains($name, '3070')
        || str_contains($name, '3090')
      ) {
        continue;
      }

      $card['name'] = cleanCardName($name);

      $priceSpan = $listItem->find('div.productPrice span', 0);
      $price = $priceSpan->getAttribute('content');
      $card['price'] = (int) $price;
      // if ($card['price'] >= 22000) { continue; }

      $href = $a->href;
      $card['url'] = "https://www.computersalg.se". $href;

      $card['status'] = ProductStatus::InStock;

      $card['restockDate'] = '';
      $card['restockDays'] = '';

      $card['source'] = "computersalg";

      $cards[$id] = $card;

    }

  }

  return $cards;

}