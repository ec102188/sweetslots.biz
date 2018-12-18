<?php

require_once '../../autoload.php';

use providerBundle\api\ProviderApi;

$client = new ProviderApi();
$jackpots = $client->getJackpots();

echo '<pre>';
var_dump($jackpots);
echo '</pre>';
