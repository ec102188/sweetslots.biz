<?php
namespace providerBundle\examples\mysqlExample;

use providerBundle\interfaces\IGameTransaction;

/**
 * Class GameTransaction
 * @package providerBundle\examples\mysqlExample
 */
class GameTransaction implements IGameTransaction
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
     * Find transaction.
     * Return Integrator's transaction ID on success or FALSE on failure.
     *
     * @param string $providerId
     * @param string $action
     * @param string $gameSessionId
     *
     * @throws \Exception
     * @return false|string
     */
    public function find($providerId, $action, $gameSessionId)
    {
        $query = "SELECT * FROM integrator.transactions
                    WHERE transaction_id = :transaction_id AND action = :action AND session_id = :session_id
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['transaction_id' => $providerId, 'action' => $action, 'session_id' => $gameSessionId]);
        if ($isSuccess !== true) {
            throw new \Exception('Could not find Transaction');
        }
        $transactionData = $stmt->fetch();

        return (isset($transactionData['id'])) ? $transactionData['id'] : false;
    }

    /**
     * Find BET transaction which has not yet been refunded.
     * Return TRUE on success or Integrator's transaction ID of REFUND transaction on failure.
     *
     * @param string $providerBetId
     * @param string $gameSessionId
     *
     * @throws \Exception
     * @return string|true
     */
    public function findSuccessBet($providerBetId, $gameSessionId)
    {
        $query = "SELECT * FROM integrator.transactions
                    WHERE transaction_id = :transaction_id AND action = 'bet' AND session_id = :session_id AND success = 1
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['transaction_id' => $providerBetId, 'session_id' => $gameSessionId]);
        if ($isSuccess !== true) {
            throw new \Exception('Could not find Transaction');
        }
        $transactionData = $stmt->fetch();

        if (is_array($transactionData) && !empty($transactionData)) {
            return true;
        } else {
            return $this->findRefundId($providerBetId, $gameSessionId);
        }
    }

    /**
     * Create new transaction.
     * Return new Integrator's transaction ID.
     *
     * @param string $providerId
     * @param string $action
     * @param string $gameSessionId
     * @param bool $success {optional} Default TRUE. If incorrect transaction, then FALSE.
     *
     * @throws \Exception
     * @return string
     */
    public function create($providerId, $action, $gameSessionId, $success = true)
    {
        $balance = $this->getBalanceId($_POST['player_id'], $_POST['currency']);

        $query = "INSERT INTO integrator.transactions
                  (player_id, balance_id, game_uuid, session_id, transaction_id, action, amount, currency, type, bet_transaction_id, success)
                  VALUES (:player_id, :balance_id, :game_uuid, :session_id, :transaction_id, :action, :amount, :currency, :type, :bet_transaction_id, :success)";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute([
            'player_id' => $_POST['player_id'],
            'balance_id' => $balance,
            'game_uuid' => $_POST['game_uuid'],
            'session_id' => $_POST['session_id'],
            'transaction_id' => $_POST['transaction_id'],
            'action' => $_POST['action'],
            'amount' => $_POST['amount'],
            'currency' => $_POST['currency'],
            'type' => isset($_POST['type']) ? $_POST['type'] : null,
            'bet_transaction_id' => isset($_POST['bet_transaction_id']) ? $_POST['bet_transaction_id'] : null,
            'success' => $success,
        ]);
        if ($isSuccess !== true) {
            throw new \Exception('Could not insert Transaction');
        }

        $newTransactionId = $this->db->lastInsertId();
        return $newTransactionId;
    }

    /**
     * Deny BET transaction after REFUND.
     *
     * @param string $providerBetId
     * @param string $gameSessionId
     *
     * @throws \Exception
     */
    public function denyBet($providerBetId, $gameSessionId)
    {
        $query = "UPDATE integrator.transactions SET success = 0
                    WHERE transaction_id = :bet_transaction_id AND action = 'bet' AND session_id = :session_id AND success = 1
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['bet_transaction_id' => $providerBetId, 'session_id' => $gameSessionId]);
        if ($stmt->rowCount() === 0) {
            throw new \Exception('Could not deny BET Transaction');
        }
    }

    /**
     * Get balance ID.
     *
     * @param string $playerId
     * @param string $currency
     *
     * @throws \Exception
     * @return int
     */
    public function getBalanceId($playerId, $currency)
    {
        $query = "SELECT * FROM integrator.balances
                    WHERE player_id = :player_id AND currency = :currency
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['player_id' => $playerId, 'currency' => $currency]);
        if ($isSuccess !== true) {
            throw new \Exception('Could not get balance ID');
        }
        $balanceId = $stmt->fetch()['id'];

        return $balanceId;
    }

    /**
     * Find Integrator's transaction ID of REFUND transaction.
     *
     * @param string $providerBetId
     * @param string $gameSessionId
     *
     * @throws \Exception
     * @return string
     */
    public function findRefundId($providerBetId, $gameSessionId)
    {
        $query = "SELECT * FROM integrator.transactions
                    WHERE transaction_id = :transaction_id AND action = 'refund' AND session_id = :session_id AND success = 1
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['transaction_id' => $providerBetId, 'session_id' => $gameSessionId]);
        if ($isSuccess !== true) {
            throw new \Exception('Could not find Transaction ID');
        }
        $transactionData = $stmt->fetch();

        return (isset($transactionData['id'])) ? $transactionData['id'] : false;
    }
}
