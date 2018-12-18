<?php
namespace providerBundle\api;

use providerBundle\interfaces\IDbTransaction;
use providerBundle\interfaces\IGameSession;
use providerBundle\interfaces\IGameTransaction;
use providerBundle\interfaces\IPlayer;
use providerBundle\exceptions\InsufficientFundsException;
use providerBundle\responses\SuccessResponse;

class Receiver
{
    /**
     * Integration data provided by Provider.
     * @var array
     */
    protected $integrationData;

    /**
     * @var IPlayer
     */
    protected $player;
    /**
     * @var IGameSession
     */
    protected $gameSession;
    /**
     * @var IGameTransaction
     */
    protected $gameTransaction;
    /**
     * @var IDbTransaction
     */
    protected $dbTransaction;

    /**
     * Receiver constructor.
     * @param IPlayer $player
     * @param IGameSession $gameSession
     * @param IGameTransaction $gameTransaction
     * @param IDbTransaction $dbTransaction
     */
    public function __construct(IPlayer $player, IGameSession $gameSession, IGameTransaction $gameTransaction, IDbTransaction $dbTransaction)
    {
        $this->integrationData = require __DIR__ . '/../config/integrationData.php';
        $this->checkRequest();

        $this->player = $player;
        $this->gameSession = $gameSession;
        $this->gameTransaction = $gameTransaction;
        $this->dbTransaction = $dbTransaction;
    }

    /**
     * @throws \Exception
     * @return SuccessResponse
     */
    public function balance()
    {
        return new SuccessResponse($this->player->getBalance($_POST['player_id'], $_POST['currency']));
    }

    /**
     * @throws InsufficientFundsException|\Exception
     * @return SuccessResponse
     */
    public function bet()
    {
        if ($transactionId = $this->gameTransaction->find($_POST['transaction_id'], 'bet', $_POST['session_id'])) {
            $balance = $this->player->getBalance($_POST['player_id'], $_POST['currency']);
        } else {
            try {
                $this->dbTransaction->begin();

                $balance = $this->player->withdraw($_POST['player_id'], $_POST['amount'], $_POST['currency']);
                $transactionId = $this->gameTransaction->create($_POST['transaction_id'], 'bet', $_POST['session_id']);
                $this->gameSession->update($_POST, $balance, $transactionId);

                $this->dbTransaction->commit();
            } catch (\Exception $e) {
                $this->dbTransaction->rollback();
                throw $e;
            }
        }

        return new SuccessResponse($balance, $transactionId);
    }

    /**
     * @throws \Exception
     * @return SuccessResponse
     */
    public function win()
    {
        $balance = $this->player->getBalance($_POST['player_id'], $_POST['currency']);
        $transactionId = $this->gameTransaction->find($_POST['transaction_id'], 'win', $_POST['session_id']);
        if ($transactionId === false) {
            try {
                $this->dbTransaction->begin();

                if ($_POST['amount'] != 0) {
                    $balance = $this->player->deposit($_POST['player_id'], $_POST['amount'], $_POST['currency']);
                }

                $transactionId = $this->gameTransaction->create($_POST['transaction_id'], 'win', $_POST['session_id']);
                $this->gameSession->update($_POST, $balance, $transactionId);

                $this->dbTransaction->commit();
            } catch (\Exception $e) {
                $this->dbTransaction->rollback();
                throw $e;
            }
        }

        return new SuccessResponse($balance, $transactionId);
    }

    /**
     * @throws \Exception
     * @return SuccessResponse
     */
    public function refund()
    {
        if ($transactionId = $this->gameTransaction->findSuccessBet($_POST['bet_transaction_id'], $_POST['session_id'])) {
            try {
                $this->dbTransaction->begin();

                $balance = $this->player->deposit($_POST['player_id'], $_POST['amount'], $_POST['currency']);
                $this->gameTransaction->denyBet($_POST['bet_transaction_id'], $_POST['session_id']);
                $transactionId = $this->gameTransaction->create($_POST['transaction_id'], 'refund', $_POST['session_id']);
                $this->gameSession->update($_POST, $balance, $transactionId);

                $this->dbTransaction->commit();
            } catch (\Exception $e) {
                $this->dbTransaction->rollback();
                throw $e;
            }
        } else {
            $balance = $this->player->getBalance($_POST['player_id'], $_POST['currency']);
            $transactionId = $this->gameTransaction->create($_POST['transaction_id'], 'refund', $_POST['session_id'], false);
        }

        return new SuccessResponse($balance, $transactionId);
    }

    /**
     * Check request from Provider.
     * @throws \Exception
     */
    protected function checkRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('All calls from Provider to Integrator will be done via POST');
        }
        $this->checkHeaders();
        $this->checkXSign();
        $this->checkPostFields();
    }

    /**
     * Check request headers.
     * @throws \Exception
     */
    protected function checkHeaders()
    {
        if ($_SERVER['CONTENT_TYPE'] !== 'application/x-www-form-urlencoded') {
            throw new \Exception('All calls from Provider to Integrator will be passed with application/x-www-form-urlencoded content type');
        }

        $requiredAuthHeaders = ['HTTP_X_MERCHANT_ID', 'HTTP_X_TIMESTAMP', 'HTTP_X_NONCE', 'HTTP_X_SIGN'];
        foreach ($requiredAuthHeaders as $headerName) {
            if (!isset($_SERVER[$headerName])) {
                $errMessage = preg_replace(['/HTTP\_/', '/\_/'], ['', '-'], $headerName) . ' header is missing';
                throw new \Exception($errMessage);
            }
        }

        if (preg_match('/\D+/', $_SERVER['HTTP_X_TIMESTAMP'])) {
            throw new \Exception('X-Timestamp header isn\'t correct');
        }

        $providerTime = $_SERVER['HTTP_X_TIMESTAMP'];
        $time = time();
        if ($providerTime > $time) {
            throw new \Exception('X-Timestamp header isn\'t correct');
        } elseif ($providerTime <= ($time - 5 * 60)) {
            throw new \Exception('Request is expired');
        }
    }

    /**
     * X-Sign validation.
     * @throws \Exception
     */
    protected function checkXSign()
    {
        $merchantKey = $this->integrationData['merchantKey'];

        $headers = [
            'X-Merchant-Id' => $_SERVER['HTTP_X_MERCHANT_ID'],
            'X-Timestamp' => $_SERVER['HTTP_X_TIMESTAMP'],
            'X-Nonce' => $_SERVER['HTTP_X_NONCE'],
        ];

        $xSign = $_SERVER['HTTP_X_SIGN'];

        $mergedParams = array_merge($_POST, $headers);
        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);

        $expectedSign = hash_hmac('sha1', $hashString, $merchantKey);

        if ($xSign !== $expectedSign) {
            throw new \Exception('X-Sign header is wrong');
        }
    }

    /**
     * Check POST fields.
     * @throws \Exception
     */
    protected function checkPostFields()
    {
        if (!isset($_POST['action'])) {
            throw new \Exception('POST field \'action\' is missing');
        } elseif (!in_array($_POST['action'], ['balance', 'bet', 'win', 'refund'])) {
            throw new \Exception('Action ' . $_POST['action'] . ' not found');
        }
    }
}
