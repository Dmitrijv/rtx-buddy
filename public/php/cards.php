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
require_once __DIR__ . '/source/webhallen.php';
require_once __DIR__ . '/source/rdebutik.php';
require_once __DIR__ . '/source/komplett.php';
require_once __DIR__ . '/source/elgiganten.php';
require_once __DIR__ . '/source/compumail.php';
require_once __DIR__ . '/source/computersalg.php';
require_once __DIR__ . '/source/datagrottan.php';
require_once __DIR__ . '/source/multitech.php';
require_once __DIR__ . '/source/sharkgaming.php';

$inetCards = getInetCards();
$primlogicCards = getPrimlogicCards();
$compliqCards = getCompliqCards();
$netonnetCards = getNetonnetCards();
$webhallenCards = getWebhallenCards();
$rdebutikCards = getRdebutikCards();
$komplettCards = getKomplettCards();
$elgigantenCards = getElgigantenCards();
$compuCards = getCompumailCards();
$salgCards = getComputersalgCards();
$grottanCards = getDatagrottanCards(); 
$multitechCards = getMultitechCards();
$sharkCards = getSharkgamingCards();

$cards = array_merge(
  $inetCards,
  $primlogicCards,
  $compliqCards,
  $netonnetCards,
  $webhallenCards,
  $rdebutikCards,
  $komplettCards,
  $elgigantenCards,
  $compuCards,
  $salgCards,
  $grottanCards,
  $multitechCards,
  $sharkCards
);

usort($cards, "compareCards");
echo json_encode($cards);

die;