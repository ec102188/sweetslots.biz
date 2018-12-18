<?php

require_once '../../autoload.php';

use providerBundle\api\ProviderApi;

$client = new ProviderApi();

$gameUrl = $client->selfValidate();

echo '<a href="' . $gameUrl . '">START GAME</a>';
