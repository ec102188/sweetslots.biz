<?php
namespace providerBundle\examples\mysqlExample;

use providerBundle\exceptions\InsufficientFundsException;
use providerBundle\interfaces\IPlayer;

/**
 * Class Player
 * @package providerBundle\examples\mysqlExample
 */
class Player implements IPlayer
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
     * Get amount of balance.
     *
     * @param string $playerId
     * @param string $currency
     *
     * @throws \Exception
     * @return double
     */
    public function getBalance($playerId, $currency)
    {
        $this->checkPlayerIdFormat($playerId);

        $query = "SELECT * FROM integrator.balances
                    WHERE player_id = :player_id AND currency = :currency
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['player_id' => $playerId, 'currency' => $currency]);
        if ($isSuccess !== true) {
            throw new \Exception('Could not get player balance');
        }
        $balance = $stmt->fetch()['amount'];

        return $balance;
    }

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
    public function deposit($playerId, $amount, $currency)
    {
        $this->checkPlayerIdFormat($playerId);

        $query = "UPDATE integrator.balances SET amount = amount + :amount
                    WHERE player_id = :player_id AND currency = :currency
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['amount' => $amount, 'player_id' => $playerId, 'currency' => $currency]);
        if ($stmt->rowCount() === 0) {
            throw new \Exception('Could not update player balance');
        }

        return $this->getBalance($playerId, $currency);
    }

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
    public function withdraw($playerId, $amount, $currency)
    {
        $query = "UPDATE integrator.balances SET amount = amount - :amount
                    WHERE player_id = :player_id AND currency = :currency AND amount >= :amount
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['amount' => $amount, 'player_id' => $playerId, 'currency' => $currency]);
        if ($stmt->rowCount() === 0) {
            throw new InsufficientFundsException('Not enough funds');
        }

        return $this->getBalance($playerId, $currency);
    }

    /**
     * Check player_id format.
     * ONLY FOR THIS EXAMPLE! Use it only if 'player_id' MUST be integer.
     * @param $playerId
     * @throws \Exception
     */
    public function checkPlayerIdFormat($playerId)
    {
        if (!preg_match('/^\d+$/', $playerId)) {
            throw new \Exception('Bad player_id format');
        }
    }
}
