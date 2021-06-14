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
  $name = str_ireplace(" GDDR6X", "", $name);
  $name = str_ireplace(" GDDR6X,", "", $name);
  $name = str_ireplace(" USB 3.0,", "", $name);
  
  // primlogic
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
  $name = str_ireplace(" 3xDP", "", $name);
  $name = str_ireplace(" 7680 x 4320 pixel,", "", $name);
  $name = str_ireplace(" 7680 x 4320 piksler", "", $name);
  $name = str_ireplace(" 7680 x 4320 pixlar", "", $name);
  $name = str_ireplace(" 384 Bit,", "", $name);
  $name = str_ireplace(" 320 bit", "", $name);
  $name = str_ireplace(" PCI Express x16 4.0", "", $name);
  $name = str_ireplace(" PCI Express 4.0", "", $name);
  $name = str_ireplace(" grafikkort", "", $name);
  
  // Misc
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
  //var_dump($input);
  echo '</pre>';
}

?>