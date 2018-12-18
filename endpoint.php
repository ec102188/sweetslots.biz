<?php

require_once '../autoload.php';

use providerBundle\api\Receiver;
use providerBundle\responses\SuccessResponse;
use providerBundle\responses\ErrorResponse;
use providerBundle\examples\mysqlExample\Player;
use providerBundle\examples\mysqlExample\GameSession;
use providerBundle\examples\mysqlExample\GameTransaction;
use providerBundle\examples\mysqlExample\DbTransaction;

header('Content-type: application/json; charset=UTF-8');

try {
    $receiver = new Receiver(new Player, new GameSession, new GameTransaction, new DbTransaction);
    /** @var SuccessResponse $successResponse */
    $successResponse = $receiver->{$_POST['action']}();

    echo $successResponse->render();
    exit;
} catch (Exception $e) {
    echo (new ErrorResponse($e))->render();
    exit;
}
