
<?php

include_once('lib/simple_html_dom.php');


// Fetch current GraphQL version from the landing page.
$prisjaktHtml = file_get_html('https://www.prisjakt.nu/')->save();
$match = preg_match('/window\.RELEASE = "(.+)";.+window\.GRAPHQL_VERSION = "(.+)";/mU', $prisjaktHtml, $matches);
if ($match !== 1) { 
  echo 'Could not fetch latest GraphQL version.';
  die;
}



$GQLRelease = $matches[1];
$GQLVersion = $matches[2];

$prisjaktEndpoint = 'https://www.prisjakt.nu/_internal/graphql?release='.$GQLRelease.'&version='.$GQLVersion.'&main=productCollection&variables={"slug":"grafikkort","type":"c","query":{"url":null,"filters":[{"id":"5530","type":"term","property":"4104"},{"id":"36254","type":"term","property":"532"}],"aggregations":[],"sort":"property.in_stock","offset":0},"productPropertyIds":["532","6716"],"productPropertyColumnIds":["532","6716"],"campaignId":4,"personalizationClientId":"","pulseEnvironmentId":""}';
$prisjaktRes = getJsonFromApi($prisjaktEndpoint);
$prisjaktJson = $prisjaktRes['data']['productCollection']['slices'][5]['products'];


$time_start = microtime(true);

//create the array of cURL handles and add to a multi_curl
$mh = curl_multi_init();
foreach($prisjaktJson as $key=>$json) {
  $id = $json['id'];
  $url = 'https://www.prisjakt.nu/_internal/graphql?release='.$GQLRelease.'&version='.$GQLVersion.'&main=product&variables={"id":'.$id.',"offset":0,"section":"main","marketCode":"se","personalizationExcludeCategories":[],"recommendationsContextId":"product-page","includeSecondary":false,"excludeTypes":["used_product","not_in_mint_condition","not_available_for_purchase"],"variants":null,"advized":true,"priceList":true,"userActions":true,"badges":true,"media":true,"campaign":true,"relatedProducts":true,"campaignDeals":true,"priceHistory":true,"campaignId":4,"personalizationClientId":"","pulseEnvironmentId":""}';

  $chs[$id] = curl_init();
  curl_setopt($chs[$id], CURLOPT_URL, $url);
  curl_setopt($chs[$id], CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($chs[$id], CURLOPT_RETURNTRANSFER, true); // Will return the response, if false it prints the response
  curl_setopt($chs[$id], CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
  ));

  curl_multi_add_handle($mh, $chs[$id]);
}



//running the requests
$running = null;
do {
  curl_multi_exec($mh, $running);
} while ($running);

//getting the responses
$prisjaktPriceNodeById = [];
foreach(array_keys($chs) as $key) {
    // $error = curl_error($chs[$key]);
    // $last_effective_URL = curl_getinfo($chs[$key], CURLINFO_EFFECTIVE_URL);
    $time = curl_getinfo($chs[$key], CURLINFO_TOTAL_TIME);
    $response = curl_multi_getcontent($chs[$key]);  // get results
    if (!empty($error)) {
      echo "The request $key return a error: $error" . "</br>";
    }
    else {
      echo "Request returned 200 in $time seconds." . "</br>";
    }

    curl_multi_remove_handle($mh, $chs[$key]);
}

// close current handler
curl_multi_close($mh);



$time_end = microtime(true);
//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = $time_end - $time_start;

//execution time of the script
echo '<b>Total Execution Time:</b> '. number_format((float) $execution_time, 10) .' Secs';
// if you get weird results, use  




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