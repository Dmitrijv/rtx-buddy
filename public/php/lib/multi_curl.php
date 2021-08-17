<?php

function getPages($urls) {

  $multi = curl_multi_init();
  $channels = [];

  // Loop through the URLs, create curl-handles
  // and attach the handles to our multi-request
  foreach ($urls as $url) {
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($handle, CURLINFO_HEADER_OUT, true);
    curl_setopt($handle, CURLOPT_REFERER, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, array(
      "Upgrade-Insecure-Requests: 1",
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
      'Accept-Encoding: br',
      'Origin: '. $url,
      'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36',
    ));
    curl_setopt($handle, CURLOPT_TIMEOUT, 5); //timeout in seconds

    curl_multi_add_handle($multi, $handle);
    $channels[$url] = $handle;
  }

  // While we're still active, execute curl
  $running = null;
  do {
    curl_multi_exec($multi, $running);
  } while ($running);

  // Loop through the channels and retrieve the received
  // content, then remove the handle from the multi-handle
  $pages = [];
  foreach ($channels as $channel) {
    $content = curl_multi_getcontent($channel);
    array_push($pages, $content);
    curl_multi_remove_handle($multi, $channel);
  }

  // Close the multi-handle and return our results
  curl_multi_close($multi);

  return $pages;

}