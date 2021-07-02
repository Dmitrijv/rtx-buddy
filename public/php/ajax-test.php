
<?php

require_once __DIR__ . '/lib/simple_html_dom.php';
require_once __DIR__ . '/lib/rtx_buddy_utils.php';
require_once __DIR__ . '/lib/phpQuery.php';
// require_once __DIR__ . '/lib/php_query_beta.php';
// require_once __DIR__ . '/lib/Snoopy.class.php';


$url1 = 'https://rdebutik.se/products/se/417/174135/sort/1/filter/0_0_0_0/GeForce-RTX-3080-Ti-Vision-OC-12GB-GDDR6X-384bit-grafikkort.html';

$url1 = 'https://rdebutik.se/o_ajax_get_product_availability_couriers_list.php?product_id=171866&lang=se';
$url2 = 'https://rdebutik.se/o_ajax_get_product_availability_couriers_list.php?product_id=174135&lang=se';

// $snoopy = new Snoopy;
// $snoopy->fetchtext('https://rdebutik.se/products/se/417/174135/sort/1/filter/0_0_0_0/GeForce-RTX-3080-Ti-Vision-OC-12GB-GDDR6X-384bit-grafikkort.html');
// pp( $snoopy->results);


//$html = file_get_html('https://rdebutik.se/products/se/417/174135/sort/1/filter/0_0_0_0/GeForce-RTX-3080-Ti-Vision-OC-12GB-GDDR6X-384bit-grafikkort.html');
// echo $html;

// $json = getJsonFromApi('https://rdebutik.se/o_ajax_get_product_availability_couriers_list.php?product_id=174135&lang=se');
// pp($json);

// $pq = phpQuery::newDocument('https://rdebutik.se/products/se/417/174135/sort/1/filter/0_0_0_0/GeForce-RTX-3080-Ti-Vision-OC-12GB-GDDR6X-384bit-grafikkort.html');

// $res = phpQuery::getJSON($url);
// $res = phpQuery::get($url);

// $doc1 = PhpQuery::ajax($url1);
// echo $doc1->find('div.avail_text');
// echo $doc1;

$doc1 = PhpQuery::get($url1);
pp($doc1->getLastResponse());

$doc2 = PhpQuery::get($url2);
pp($doc2->getLastResponse());
// echo $doc2->find('div.avail_text');

// pp($res);

// pp($pq);

// phpQuery::getJSON('https://rdebutik.se/o_ajax_get_product_availability_couriers_list.php?product_id=174135&lang=se', null, 'sayHi');

// function sayHi($param) {
//     pp($param);
// }
    

die;

?>