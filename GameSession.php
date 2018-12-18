<?php
namespace providerBundle\examples\mysqlExample;

use providerBundle\interfaces\IGameSession;

/**
 * Class GameSession
 * @package providerBundle\examples\mysqlExample
 */
class GameSession implements IGameSession
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
     * Update game session.
     *
     * @param array $postParams
     * @param double $playerBalanceAfterTransaction
     * @param string $transactionId
     * @throws \Exception
     */
    public function update($postParams, $playerBalanceAfterTransaction, $transactionId)
    {
        switch ($_POST['action']) {
            case 'bet':
                $addBet = $_POST['amount'];
                break;
            case 'win':
                $addWin = $_POST['amount'];
                break;
            case 'refund':
                $addBet = '-' . $_POST['amount'];
                break;
            default:
                break;
        }

        $query = "UPDATE integrator.sessions
                    SET end_time = NOW(), total_bet = total_bet + :add_bet, total_win = total_win + :add_win
                    WHERE id = :id
                    LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'add_bet' => (isset($addBet)) ? $addBet : 0,
            'add_win' => (isset($addWin)) ? $addWin : 0,
            'id' => $_POST['session_id'],
        ]);
        if ($stmt->rowCount() === 0) {
            throw new \Exception('Could not update Session');
        }
    }
}
