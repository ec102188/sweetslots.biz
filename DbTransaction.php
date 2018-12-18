<?php
namespace providerBundle\examples\mysqlExample;

use providerBundle\interfaces\IDbTransaction;

/**
 * Class DbTransaction
 * @package providerBundle\examples\mysqlExample
 */
class DbTransaction implements IDbTransaction
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->db = DbConnect::getPdo();
    }

    /**
     * Initiates database transaction.
     * @throws \Exception
     */
    public function begin()
    {
        $isSuccess = $this->db->beginTransaction();
        if ($isSuccess !== true) {
            throw new \Exception('Could not begin DB Transaction');
        }
    }

    /**
     * Commits database transaction.
     * @throws \Exception
     */
    public function commit()
    {
        $isSuccess = $this->db->commit();
        if ($isSuccess !== true) {
            throw new \Exception('Could not commit DB Transaction');
        }
    }

    /**
     * Rolls back database transaction.
     */
    public function rollback()
    {
        $this->db->rollBack();
    }
}
