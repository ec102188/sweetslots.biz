<?php

require_once '../../autoload.php';

use providerBundle\api\ProviderApi;

$client = new ProviderApi();

$gameUuid = '';
$playerId = '';
$playerName = '';
$currency = '';
$sessionId = '';

$gameUrl = $client->initGame($gameUuid, $playerId, $playerName, $currency, $sessionId);

echo '<a href="' . $gameUrl . '">START GAME</a>';
