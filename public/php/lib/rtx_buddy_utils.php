<?php


abstract class ProductStatus {
  const KnownStock = 0;
  const InStock = 1;
  const Incoming = 2;
  const Delayed = 3;
  const SoldOut = 4;
  const Blocked = 5;
  const Na = 6;
}




function cleanCardName($name) {

  $name = str_ireplace("TI", "Ti", $name);
  $name = str_ireplace("ti", "Ti", $name);
  $name = str_ireplace("RTX 3080Ti GAMING TRIO", "", $name);
  $name = str_ireplace(" ()", "", $name);
  // Common
  $name = str_ireplace("GeForce RTX 3080", "", $name);
  $name = str_ireplace(" RTX 3080", "", $name);
  $name = str_ireplace(" NVIDIA", "", $name);
  $name = str_ireplace(" 10 GB", "", $name);
  $name = str_ireplace(" 12 GB", "", $name);
  $name = str_ireplace(" 10Gb", "", $name);
  $name = str_ireplace(" 12Gb", "", $name);
  $name = str_ireplace(" 10G", "", $name);
  $name = str_ireplace(" 12G", "", $name);
  $name = str_ireplace(" GDDR5,", "", $name);
  $name = str_ireplace(" GDDR6,", "", $name);
  $name = str_ireplace(" GDDR6X", "", $name);
  $name = str_ireplace(" USB 3.0,", "", $name);
  $name = str_ireplace(" RGB", "", $name);
  $name = str_ireplace(" 1.4a", "", $name);
  $name = str_ireplace(" 2.1", "", $name);
  
  // primlogic
  $name = str_ireplace(" Fusion", "", $name);
  $name = str_ireplace(" Aura Sync RGB", "", $name);
  $name = str_ireplace(" / Ej bokningsbart!", "", $name);
  $name = str_ireplace(" externt grafikkort Thunderbolt 3,", "", $name);
  $name = str_ireplace(" RGB Fusion 2.0", "", $name);
  $name = str_ireplace(" (LHR)", "", $name);
  $name = str_ireplace(" LAN,", "", $name);
  $name = str_ireplace(" 2xHDMI/2xDP", "", $name);
  $name = str_ireplace(" 2xHDMI/3xDP", "", $name);
  $name = str_ireplace(" 3xHDMI/3xDP", "", $name);
  $name = str_ireplace(" HDMI/3xDP", "", $name);
  $name = str_ireplace("2xHDMI,", "", $name);  
  $name = str_ireplace(" HDMI", "", $name);
  $name = str_ireplace(" 1xHDMI", "", $name);
  $name = str_ireplace(" 2xHDMI", "", $name);  
  $name = str_ireplace(" 3xHDMI", "", $name);  
  $name = str_ireplace(" /2xDP", "", $name);
  $name = str_ireplace(" /3xDP", "", $name);

  // CDON
  $name = str_ireplace(" EdiTion 3x DisplayPort 1x 2x 8-Pin", "", $name);
  $name = str_ireplace(" 2xDP", "", $name);
  $name = str_ireplace(" 3xDP", "", $name);
  $name = str_ireplace(" 7680 x 4320 pixel,", "", $name);
  $name = str_ireplace(" 7680 x 4320 piksler", "", $name);
  $name = str_ireplace(" 7680 x 4320 pixlar", "", $name);
  $name = str_ireplace(" 384 Bit,", "", $name);
  $name = str_ireplace(" 320 bit", "", $name);
  $name = str_ireplace(" PCI Express x16 4.0", "", $name);
  $name = str_ireplace(" PCI Express 4.0", "", $name);
  $name = str_ireplace(" PCIe 4.0", "", $name);
  $name = str_ireplace(" grafikkort", "", $name);
  
  // compliq
  $name = str_ireplace(" GDDR5", "", $name);
  $name = str_ireplace(" GDDR6", "", $name);
  $name = str_ireplace(" DPX3X2", "", $name);
  $name = str_ireplace(" CTLR", "", $name);
  $name = str_ireplace(" DP", "", $name);
  $name = str_ireplace(" - GF", "", $name);
  $name = str_ireplace(" x16", "", $name);
  $name = str_ireplace(" graphics card", "", $name);
  $name = str_ireplace("-RTX3080-O10G-WHITE -", "", $name);
  $name = str_ireplace("-RTX3080", " ", $name);
  $name = str_ireplace("-O12G", "", $name);
  $name = str_ireplace("(NED308T019KB-1020G)", "", $name);
  $name = str_ireplace("(38IYM3MD99SK)", "", $name);
  $name = str_ireplace("/HDMI GeForce RTXâ„¢ 3080 EdiTion", "", $name);
  $name = str_ireplace(" 3 x DisplayPort", "", $name);
  $name = str_ireplace(" RGB GAMING", "", $name);
  $name = str_ireplace(" RTX3080", "", $name); // ASUS VGA Asus ROG Strix
  $name = str_ireplace(" 1.4", "", $name); // ASUS VGA Asus ROG Strix
  $name = str_ireplace(" O10G V2-GAMING 2", "", $name);
  $name = str_ireplace(" CI-Express 4.0", "", $name);

  $name = str_ireplace(" EPIC-X GAMING EdiTion", "", $name);
  $name = str_ireplace(" vit", "", $name);
  $name = str_ireplace(" 2 x", "", $name);
  $name = str_ireplace(" 3 x", "", $name);
  $name = str_ireplace(" 3x", "", $name);
  $name = str_ireplace(" 2.0", "", $name);
  $name = str_ireplace(" P", "", $name);
  $name = str_ireplace(" -", "", $name);

  $name = str_ireplace(" RTX3080Ti", "", $name);
  $name = str_ireplace(" OC-12G/2xHDMI", "", $name);

  // PriceRunner
  $name = str_ireplace(" Epic-X", "", $name);
  $name = str_ireplace(" Microsystems", "", $name);
  $name = str_ireplace(" GameRock", "", $name);
  
  // Rdebutik
  $name = str_ireplace(" 320bit", "", $name);
  $name = str_ireplace(" 384bit", "", $name);

  // Misc
  $name = str_ireplace(" 1-click OC", " ", $name);
  $name = str_ireplace(" 1-Click OC", " ", $name);
  $name = str_ireplace(" (1-Click OC)", " ", $name);
  $name = str_ireplace("  ", " ", $name);
  $name = str_ireplace(",", " ", $name);
  $name = str_ends_with($name, ' Ti') ? substr($name, 0, -3) : $name;

  $name = implode(' ',array_unique(explode(' ', $name)));

  return $name;
}

function compareCards($a, $b) {

  // if both cards are in stock (whether stock is known or not) sort by price
  if ($a['status'] <= ProductStatus::InStock && $b['status'] <= ProductStatus::InStock) {
    return $a['price'] > $b['price'] ? 1 : -1;
  }

  if ($a['status'] == $b['status']) {
    if ($a['status'] == ProductStatus::Incoming) { 
      if (strlen($a['restockDate']) < 10) { return 1; }
      if (strlen($b['restockDate']) < 10) { return -1; }
      if ($a['restockDays'] == $b['restockDays']) return $a['price'] > $b['price'] ? 1 : -1;
      return $a['restockDays'] > $b['restockDays'] ? 1 : -1;
    }
    return $a['price'] > $b['price'] ? 1 : -1;
  }
  return $a['status'] > $b['status'] ? 1 : -1;
}

function getJsonFromApi($endpoint, $body = null) {

  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $endpoint);
  curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true); // Will return the response, if false it prints the response
  curl_setopt($handle, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
  ));
  
  if ($body !== null) {
    curl_setopt($handle, CURLOPT_POSTFIELDS, "['". implode("', '", $body) ."']");
  }

  $result = curl_exec($handle);
  curl_close($handle);

  return json_decode($result, true);
}

function mimicAjax($endpoint) {
  $curl = curl_init($endpoint);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "Content-Type: application/json; charset=utf-8"));
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
  $result = curl_exec($curl);
  return json_decode($result, true);
}

function getDaysToDate($target) {
  $now = date("Y/m/d");
  $start = new DateTime($now);
  $end = new DateTime($target);
  return $end->diff($start)->format("%a");
}

function getDateByDaysLeft($days) {
  return null;
}

function pp($input) {
  echo '<pre>';
  print_r($input);
  // var_dump($input);
  echo '</pre>';
}

?>