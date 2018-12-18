<?php

require_once '../../autoload.php';

use providerBundle\api\ProviderApi;

$client = new ProviderApi();

$gameUuid = '';

$gameUrl = $client->initDemoGame($gameUuid);

echo '<a href="' . $gameUrl . '">START GAME</a>';
