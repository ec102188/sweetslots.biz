<?php
namespace providerBundle\interfaces;

use providerBundle\exceptions\InsufficientFundsException;

/**
 * Interface IPlayer
 * @package providerBundle\interfaces
 */
interface IPlayer
{
    /**
     * Get amount of balance.
     *
     * @param string $playerId
     * @param string $currency
     *
     * @throws \Exception
     * @return double
     */
    function getBalance($playerId, $currency);

    /**
     * Increase amount of balance in database.
     * Return balance after deposit.
     *
     * @param string $playerId
     * @param string $amount
     * @param string $currency
     *
     * @throws \Exception
     * @return double
     */
    function deposit($playerId, $amount, $currency);

    /**
     * Decrease amount of balance in database.
     * Return balance after withdraw.
     *
     * @param string $playerId
     * @param string $amount
     * @param string $currency
     *
     * @throws \Exception|InsufficientFundsException
     * @return double
     */
    function withdraw($playerId, $amount, $currency);
}
