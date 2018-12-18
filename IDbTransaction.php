<?php
namespace providerBundle\interfaces;

/**
 * Interface IDbTransaction
 * @package providerBundle\interfaces
 */
interface IDbTransaction
{
    /**
     * Initiates database transaction.
     * @throws \Exception
     */
    function begin();

    /**
     * Commits database transaction.
     * @throws \Exception
     */
    function commit();

    /**
     * Rolls back database transaction.
     */
    function rollback();
}
