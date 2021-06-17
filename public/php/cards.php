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

$inetCards = getInetCards();
$prisjaktCards = getPrisjaktCards(); 
$primlogicCards = getPrimlogicCards();
$cdonCards = getCdonCards();
$compliqCards = getCompliqCards();
$netonnetCards = getNetonnetCards();

$cards = array_merge($inetCards, $prisjaktCards, $primlogicCards, $cdonCards, $compliqCards, $netonnetCards);
$cards = array_filter($cards, "isRequested");

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