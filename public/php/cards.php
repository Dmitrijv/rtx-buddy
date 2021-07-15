<?php

// header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Origin: http://dmitrijv.se');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once __DIR__ . '/source/inet.php';
require_once __DIR__ . '/source/primlogic.php';
require_once __DIR__ . '/source/compliq.php';
require_once __DIR__ . '/source/netonnet.php';
require_once __DIR__ . '/source/pricerunner.php';
require_once __DIR__ . '/source/webhallen.php';
require_once __DIR__ . '/source/rdebutik.php';
require_once __DIR__ . '/source/komplett.php';
require_once __DIR__ . '/source/elgiganten.php';

$inetCards = getInetCards();
$primlogicCards = getPrimlogicCards();
$compliqCards = getCompliqCards();
$netonnetCards = getNetonnetCards();
$pricerunnerCards = getPricerunnerCards();
$webhallenCards = getWebhallenCards();
$rdebutikCards = getRdebutikCards();
$komplettCards = getKomplettCards();
$elgigantenCards = getElgigantenCards();

$cards = array_merge(
  $inetCards,
  $primlogicCards,
  $compliqCards,
  $netonnetCards,
  $pricerunnerCards,
  $webhallenCards,
  $rdebutikCards,
  $komplettCards,
  $elgigantenCards
);

usort($cards, "compareCards");
echo json_encode($cards);

die;