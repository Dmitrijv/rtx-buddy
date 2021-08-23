<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';
require_once __DIR__ . '/../lib/multi_curl.php';

function getMultitechCards() {

  $blacklist = [];
  $blacklist['p1001573951'] = true; // GIGABYTE AORUS RTX 3080 GAMING BOX (GV-N3080IXEB-10GD)
  $blacklist['p1002035090'] = true; // ASUS GeForce RTX 3080 TI 12GB GDDR6X ROG STRIX LC GAMING (LHR) (90YV0GT3-M0NM00)
  $blacklist['p1002035089'] = true; // ASUS GeForce RTX 3080 TI 12GB GDDR6X ROG STRIX LC OC GAMING (LHR) (90YV0GT2-M0NM00)
  $blacklist['p1002025921'] = true; // ASUS ROG GC31S-030- ROG Flow X13 Docking Station RTX 3080 (GC31S-030)
  $blacklist['p1001570623'] = true; // EVGA RTX 3080 FTW3 ULTRA HYBRID GAMING 10GB (10G-P5-3898-KR)
  $blacklist['p1001570623'] = true; // EVGA RTX 3080 XC3 ULTRA HYBRID GAMING 10GB (10G-P5-3888-KR)
  $blacklist['p1001547466'] = true; // GIGABYTE VGA GBT RTX3080 10GB XTREME WATERFORCE 10G 2 (GV-N3080AORUSX W-10GD)
  $blacklist['p1001547469'] = true; // GIGABYTE VGA GBT RTX3080 10GB XTREME WATERFORCE WB 10G 2 (GV-N3080AORUSX WB-10GD)

  $cards = [];

  $html = file_get_html("https://webshop.multitech.se/multi/search/c_36132/ps_90?kw=rtx+3080");
  if($html === false) { return []; }

  $urlsToMultiCurl = [];

  foreach($html->find('tr[valign=top]') as $listItem) {

    $a = $listItem->find('a.pn', 0);
    if (!is_object($a)) {
      continue;
    }

    $urlTail = $a->getAttribute('href');

    preg_match_all('/\/(p\d+)$/m', $urlTail, $matches, PREG_SET_ORDER, 0);
    $id = $matches[0][1];

    if (array_key_exists($id, $blacklist) == true || array_key_exists($id, $cards) == true) {
      continue;
    }

    $url = 'https://webshop.multitech.se/' . $urlTail;

    $name = $a->plaintext;
    $name = cleanCardName($name);
    
    $isInStock = false;
    foreach($listItem->find('img') as $element) {
      $src = $element->getAttribute('src');
      if ($src === 'https://asset1-327a.kxcdn.com/w548290/Skins/Default/Img/sv/icon-instock.v784612c9.gif') {
        $isInStock = true;
        break;
      }
    }    

    if ($isInStock === true) {

      $card['status'] = ProductStatus::InStock;
      
      $card['url'] = $url;

      $price = $listItem->find('span.pricecat', 0)->plaintext;
      $price = str_ireplace(".", "", $price);
      $price = str_ireplace(":-", "", $price);
      $card['price'] = (int) $price;

      $card['name'] = $name;
      
      $card['restockDate'] = '';
      $card['restockDays'] = '';

      $card['source'] = "multitech";

      $cards[$id] = $card;

    } else {
      array_push($urlsToMultiCurl, $url);
      continue;
    }

  }

  // $unknownPages = getPages($urlsToMultiCurl);

  // foreach ($unknownPages as $key=>$string) {

  //   $html = new simple_html_dom();
  //   $html->load($string);

  //   $url = $urlsToMultiCurl[$key];
  //   preg_match_all('/\/(p\d+)$/m', $url, $matches, PREG_SET_ORDER, 0);
  //   $id = $matches[0][1];

  //   if (array_key_exists($id, $blacklist) == true || array_key_exists($id, $cards) == true) {
  //     continue;
  //   }

  //   $stock = $html->find('.stock b', 0);
  //   if (!is_object($stock)) {
  //     continue;
  //   }

  //   $date = $stock->plaintext;

  //   preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date, $matches);
  //   if (!array_key_exists(0, $matches)) {
  //     continue;
  //   }

  //   $card['status'] = ProductStatus::Incoming;
    
  //   $date = $matches[0];
  //   $card['restockDate'] = $date;
  //   $card['restockDays'] = getDaysToDate($date);

    
  //   $card['url'] = $url;
    
  //   $price = $html->find('span.pricedetails', 0)->plaintext;
  //   $price = str_ireplace(".", "", $price);
  //   $price = str_ireplace(":-", "", $price);
  //   $card['price'] = (int) $price;

  //   $name = $html->find('h1.partname', 0)->plaintext;
  //   $name = cleanCardName($name);
  //   $card['name'] = $name;

  //   $card['source'] = "multitech";

  //   $cards[$id] = $card;

  // }

  return $cards;

}