<?php

return [
    // Database configuration
    'dsn' => 'mysql:host=127.0.0.1;dbname=integrator',
    'username' => 'root',
    'password' => '',
    'options' => [
        'PDO::ATTR_ERRMODE' =>'PDO::ERRMODE_EXCEPTION',
    ],
];
