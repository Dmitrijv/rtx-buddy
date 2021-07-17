<?php

require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../lib/rtx_buddy_utils.php';

function getComputersalgCards() {

    $urls = [
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=1',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=2',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=3',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=4',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=5',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=6',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=7',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=8',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=9',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=10',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=11',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=12',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=13',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=14',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=15',
    'https://www.computersalg.se/l/5444/alla-grafikkort?csstock=0&f=40439e8c-d257-411a-8b77-82112a409e61&sq=rtx%203080&p=16',
    ];

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


    $blacklist = [];
    $blacklist[7000620] = true; // Gamebox
    $blacklist[7353786] = true; // 3060
    $blacklist[7438330] = true; // 3060
    $blacklist[7376745] = true; // 3060 Ti
    $blacklist[7373227] = true; // 3070 Ti
    $blacklist[7383417] = true; // 3070 Ti
    $blacklist[7384457] = true; // 3070 Ti
    $blacklist[7383056] = true; // 3070 Ti
    $blacklist[7466819] = true; // 3080 Waterforce
    $blacklist[7461632] = true; // 3080 Waterforce
    $blacklist[7261641] = true; // 3080 Waterforce
    $blacklist[7466818] = true; // 3080 Waterforce
    $blacklist[6813126] = true; // 3080 Waterforce
    $blacklist[7373217] = true; // 3080 Hybrid
    $blacklist[7341368] = true; // 3090

    $cards = [];

    foreach ($pages as $string) {
    
    $html = new simple_html_dom();
    $html->load($string);

    // build a list of cards
    foreach($html->find('ul.productlist li.productlist-item') as $index=>$listItem) {

        $a = $listItem->find('a.productNameLink', 0);
        $id = $a->getAttribute('data-toitemid');
        if (array_key_exists($id, $blacklist) == true) {
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
        if ($card['price'] >= 22000) {
          continue;
        }

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