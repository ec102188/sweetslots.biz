<?php
namespace providerBundle\interfaces;

/**
 * Interface IGameSession
 * @package providerBundle\interfaces
 */
interface IGameSession
{
    /**
     * Update game session.
     *
     * @param array $postParams
     * @param double $playerBalanceAfterTransaction
     * @param string $transactionId
     * @throws \Exception
     */
    function update($postParams, $playerBalanceAfterTransaction, $transactionId);
}
