<?php

// header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Origin: http://dmitrijv.se');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once __DIR__ . '/source/inet.php';
require_once __DIR__ . '/source/primlogic.php';
require_once __DIR__ . '/source/prisjakt.php';
require_once __DIR__ . '/source/cdon.php';
require_once __DIR__ . '/source/compliq.php';
require_once __DIR__ . '/source/netonnet.php';
require_once __DIR__ . '/source/pricerunner.php';
require_once __DIR__ . '/source/webhallen.php';
require_once __DIR__ . '/source/rdebutik.php';
require_once __DIR__ . '/source/komplett.php';

$inetCards = getInetCards();
$prisjaktCards = []; // getPrisjaktCards();
$primlogicCards = getPrimlogicCards();
$cdonCards = getCdonCards();
$compliqCards = getCompliqCards(); // getCompliqCards(); [];
$netonnetCards = getNetonnetCards();
$pricerunnerCards = getPricerunnerCards();
$webhallenCards = getWebhallenCards();
$rdebutikCards = getRdebutikCards();
$komplettCards = getKomplettCards();

$cards = array_merge(
  $inetCards,
  $prisjaktCards,
  $primlogicCards,
  $cdonCards,
  $compliqCards,
  $netonnetCards,
  $pricerunnerCards,
  $webhallenCards,
  $rdebutikCards,
  $komplettCards
);

$cards = array_filter($cards, "isRequested");

// remove potential duplicates
// foreach ($cards as $key => $card) {
//   $similarCards = array_filter($cards, function($c) use ($card) {
//       return $card['source'] !== $c['source'] && trim($card['name']) == trim($c['name']) && $card['price'] == $c['price'];
//     }
//   );

//   if (count($similarCards) > 0) {
//     unset($cards[$key]);
//   }
// }

usort($cards, "compareCards");
echo json_encode($cards);

die;

abstract class RequestType {
  const InStockOnly = 0;
  const IncludeIncoming = 1;
  const ShowAll = 2;
}

function isRequested($card) {
  return $card['status'] <= ProductStatus::Incoming;
}

?>