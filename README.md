# Game Provider SDK
PHP devkit for Provider API integration.

## Table of Contents
1. [Usage](#usage)
    1. [Configuration](#configuration)
    2. [Provider API (Integrator ➜ Provider ➜ Integrator)](#provider-api)
    3. [Receiver (Provider ➜ Integrator ➜ Provider)](#receiver)
2. [Directory structure](#directory-structure)

## Usage

### Configuration
Set Integration data provided by Provider (`providerBundle/config/integrationData.php`):
```php
'merchantId' => '{PROVIDER-MERCHANT-ID}',
'merchantKey' => '{PROVIDER-MERCHANT-KEY}',
'baseApiUrl' => '{PROVIDER-API-URL}',
```

### Provider API
Make api calls to Provider using 'ProviderApi' class (`providerBundle/api/ProviderApi.php`):
```php
$client = new ProviderApi();
$games = $client->getGames();
```
'playground' folder contains examples of api calls to Provider (`providerBundle/examples/playground/`).

### Receiver
Endpoint processes all calls from Provider to your application (`providerBundle/examples/endpoint.php`).
Write your own implementations of interfaces 'IPlayer', 'IGameTransaction', 'IGameSession.php' and 'IDbTransaction.php' (`providerBundle/interfaces/`).
Type in own objects as parameters of 'Receiver' object:
```php
$receiver = new Receiver(new Player, new GameSession, new GameTransaction, new DbTransaction);
```
'mysqlExample' folder contains example of implementation with MySQL database (`providerBundle/examples/mysqlExample/`).

## Directory Structure
[providerBundle/](#providerbundle)  
├ [api/](#providerbundle-api)  
│ ├ [ProviderApi.php](#providerbundle-api-providerapi-php)  
│ └ [Receiver.php](#providerbundle-receiver-php)  
├ [config/](#providerbundle-config)  
│ └ [integrationData.php](#providerbundle-integrationdata-php)  
├ [examples/](#providerbundle-examples)  
│ ├ [mysqlExample/](#providerbundle-examples-mysqlexample)  
│ │ ├ [config/](#providerbundle-examples-mysqlexample-config)  
│ │ │ └ [db.php](#providerbundle-examples-mysqlexample-config-db-php)  
│ │ ├ [DbConnect.php](#providerbundle-examples-mysqlexample-dbconnect-php)  
│ │ ├ [dbExample.sql](#providerbundle-examples-mysqlexample-dbexample-php)  
│ │ ├ [DbTransaction.php](#providerbundle-examples-mysqlexample-dbtransaction-php)  
│ │ ├ [GameSession.php](#providerbundle-examples-mysqlexample-gamesession-php)  
│ │ ├ [GameTransaction.php](#providerbundle-examples-mysqlexample-gametransaction-php)  
│ │ └ [Player.php](#providerbundle-examples-mysqlexample-player-php)  
│ ├ [playground/](#providerbundle-examples-playground)  
│ │ ├ [games.php](#providerbundle-examples-playground-games-php)  
│ │ ├ [jackpots.php](#providerbundle-examples-playground-jackpots-php)  
│ │ ├ [limits.php](#providerbundle-examples-playground-limits-php)  
│ │ ├ [playDemo.php](#providerbundle-examples-playground-playdemo-php)  
│ │ ├ [playReal.php](#providerbundle-examples-playground-playreal-php)  
│ │ └ [selfValidate.php](#providerbundle-examples-playground-selfvalidate-php)  
│ └ [endpoint.php](#providerbundle-examples-endpoint-php)  
├ [exceptions/](#providerbundle-exceptions)  
│ └ [InsufficientFundsException.php](#providerbundle-exceptions-insufficientfundsexception-php)  
├ [interfaces/](#providerbundle-interfaces)  
│ ├ [IDbTransaction.php](#providerbundle-interfaces-idbtransaction-php)  
│ ├ [IGameSession.php](#providerbundle-interfaces-igamesession-php)  
│ ├ [IGameTransaction.php](#providerbundle-interfaces-igametransaction-php)  
│ └ [IPlayer.php](#providerbundle-interfaces-iplayer-php)  
├ [responses/](#providerbundle-responses)  
│ ├ [ErrorResponse.php](#providerbundle-responses-errorresponse-php)  
│ └ [SuccessResponse.php](#providerbundle-responses-successresponse-php)  
└ [autoload.php](#providerbundle-autoload-php)  

### providerBundle/
SDK directory.

### providerBundle/api/
Contains SDK API models.

### providerBundle/api/ProviderApi.php
Class, which is used to manage Provider API methods (Integrator ➜ Provider ➜ Integrator).

### providerBundle/api/Receiver.php
Class, which is used to process Provider requests (Provider ➜ Integrator ➜ Provider).

### providerBundle/config/
Contains configuration files.

### providerBundle/config/integrationData.php
Configuration file contains integration data provided by Provider.

### providerBundle/examples/
Contains examples of SDK implementation.

### providerBundle/examples/mysqlExample/
Contains example of SDK implementation using MySQL as data storage.

### providerBundle/examples/mysqlExample/config/
Contains configuration files of this implementation.

### providerBundle/examples/mysqlExample/config/db.php
Contains database configuration (dsn, username, password, options).

### providerBundle/examples/mysqlExample/DbConnect.php
Contains PDO connection.

### providerBundle/examples/mysqlExample/dbExample.sql
MySQL database structure of this implementation.

### providerBundle/examples/mysqlExample/DbTransaction.php
Implementation of interface 'IDbTransaction'.

### providerBundle/examples/mysqlExample/GameSession.php
Implementation of interface 'IGameSession'.

### providerBundle/examples/mysqlExample/GameTransaction.php
Implementation of interface 'IGameTransaction'.

### providerBundle/examples/mysqlExample/Player.php
Implementation of interface 'IPlayer'.

### providerBundle/examples/playground
Contains examples of api calls to Provider.

### providerBundle/examples/playground/games.php
Contains example of retrieving games list.

### providerBundle/examples/playground/jackpots.php
Contains example of retrieving jackpots list.

### providerBundle/examples/playground/limits.php
Contains example of retrieving limits list.

### providerBundle/examples/playground/playDemo.php
Contains example of initializing demo game session.

### providerBundle/examples/playground/playReal.php
Contains example of initializing game session.

### providerBundle/examples/playground/selfValidate.php
Contains example of self validation.

### providerBundle/examples/endpoint.php
Requests from Provider to Integrator should be done to this endpoint. (Example).

### providerBundle/exceptions/
Contains exceptions.

### providerBundle/exceptions/InsufficientFundsException.php
Exception for Provider error code 'INSUFFICIENT_FUNDS'. Used in 'bet' action when player has insufficient funds.

### providerBundle/interfaces/
Contains interfaces for custom classes.

### providerBundle/interfaces/IDbTransaction.php
Interface for custom class which is used to manage DB transactions.

### providerBundle/interfaces/IGameSession.php
Interface for custom class which is used to manage game sessions.

### providerBundle/interfaces/IGameTransaction.php
Interface for custom class which is used to manage game transactions.

### providerBundle/interfaces/IPlayer.php
Interface for custom class which is used to manage player.

### providerBundle/responses/
Contains responses to Provider.

### providerBundle/responses/ErrorResponse.php
Render 'error' response to Provider.

### providerBundle/responses/SuccessResponse.php
Render 'success' response to Provider.

### providerBundle/autoload.php
Autoloading Classes.
