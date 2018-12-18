<?php

require_once '../../autoload.php';

use providerBundle\api\ProviderApi;

$client = new ProviderApi();
$games = $client->getGames();

echo '<pre>';
var_dump($games);
echo '</pre>';
