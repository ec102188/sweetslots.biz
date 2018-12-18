<?php

require_once '../../autoload.php';

use providerBundle\api\ProviderApi;

$client = new ProviderApi();
$limits = $client->getLimits();

echo '<pre>';
var_dump($limits);
echo '</pre>';
