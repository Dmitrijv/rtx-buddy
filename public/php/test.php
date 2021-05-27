
<?php

include_once('lib/simple_html_dom.php');

$url = 'https://www.prisjakt.nu/';
$res = getJsonFromApi($priceEndpoint);

pp($res);

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


function pp($input) {
  echo '<pre>';
  print_r($input);
  //var_dump($input);
  echo '</pre>';
}


die;

?>