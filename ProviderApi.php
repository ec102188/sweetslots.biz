<?php
namespace providerBundle\api;

/**
 * Class ProviderApi
 * @package providerBundle\api
 */
class ProviderApi
{
    /**
     * Integration data provided by Provider.
     * @var array
     */
    protected $integrationData;

    /**
     * ProviderApi constructor.
     */
    public function __construct()
    {
        $this->integrationData = require __DIR__ . '/../config/integrationData.php';
    }

    /**
     * Provider API
     * Retrieving games list.
     * Method: /games
     * @param int $page
     * @return array
     */
    public function getGames($page = 1)
    {
        $requestParams = ['page' => $page];

        $requestParamsStr = http_build_query($requestParams);
        $authHeaders = $this->getAuthHeaders($requestParams);
        $url = $this->integrationData['baseApiUrl'] . '/games?' . $requestParamsStr;
        $result = $this->sendRequest($authHeaders, $url);

        return $result;
    }

    /**
     * Provider API
     * Returns list of tables for the selected game.
     * Method: /games/lobby
     * @param string $gameUuid
     * @param string $currency
     * @return array
     */
    public function getLobbies($gameUuid, $currency)
    {
        $requestParams = ['game_uuid' => $gameUuid, 'currency' => $currency];

        $requestParamsStr = http_build_query($requestParams);
        $authHeaders = $this->getAuthHeaders($requestParams);
        $url = $this->integrationData['baseApiUrl'] . '/games/lobby?' . $requestParamsStr;
        $result = $this->sendRequest($authHeaders, $url);

        return $result;
    }

    /**
     * Provider API
     * Initializing game session.
     * Method: games/init
     * @param string $gameUuid
     * @param string $playerId
     * @param string $playerName
     * @param string $currency
     * @param string $sessionId
     * @param null|string $returnUrl [optional]
     * @param null|string $language [optional]
     * @param null|string $email [optional]
     * @param null|string $lobbyData [optional]
     * @return string
     */
    public function initGame(
        $gameUuid,
        $playerId,
        $playerName,
        $currency,
        $sessionId,
        $returnUrl = null,
        $language = null,
        $email = null,
        $lobbyData = null
    )
    {
        $requestParams = [
            'game_uuid' => $gameUuid,
            'player_id' => $playerId,
            'player_name' => $playerName,
            'currency' => $currency,
            'session_id' => $sessionId,
            'return_url' => $returnUrl,
            'language' => $language,
            'email' => $email,
            'lobby_data' => $lobbyData,
        ];

        $authHeaders = $this->getAuthHeaders($requestParams);
        $url = $this->integrationData['baseApiUrl'] . '/games/init';
        $gameUrl = $this->sendRequest($authHeaders, $url, 'POST', $requestParams)['url'];

        return $gameUrl;
    }

    /**
     * Provider API
     * Initializing demo game session (only if Provider has demo mode).
     * Method: games/init-demo
     * @param string $gameUuid
     * @param null|string $returnUrl [optional]
     * @param null|string $language [optional]
     * @return string
     */
    public function initDemoGame($gameUuid, $returnUrl = null, $language = null)
    {
        $requestParams = ['game_uuid' => $gameUuid, 'return_url' => $returnUrl, 'language' => $language];

        $authHeaders = $this->getAuthHeaders($requestParams);
        $url = $this->integrationData['baseApiUrl'] . '/games/init-demo';
        $demoGameUrl = $this->sendRequest($authHeaders, $url, 'POST', $requestParams)['url'];

        return $demoGameUrl;
    }

    /**
     * Provider API
     * Returns list of limits for merchant.
     * Method: /limits
     * @return array
     */
    public function getLimits()
    {
        $authHeaders = $this->getAuthHeaders();
        $url = $this->integrationData['baseApiUrl'] . '/limits';
        $result = $this->sendRequest($authHeaders, $url);

        return $result;
    }

    /**
     * Provider API
     * Returns list of jackpots assigned to merchant key.
     * Method: /jackpots
     * @return array
     */
    public function getJackpots()
    {
        $authHeaders = $this->getAuthHeaders();
        $url = $this->integrationData['baseApiUrl'] . '/jackpots';
        $result = $this->sendRequest($authHeaders, $url);

        return $result;
    }

    /**
     * Provider API
     * Self validation.
     * Method: /self-validate
     * @param bool $debugValidate
     * @return array
     */
    public function selfValidate($debugValidate = true)
    {
        $requestParams = [
            'debug_validate' => $debugValidate,
        ];

        $authHeaders = $this->getAuthHeaders($requestParams);
        $url = $this->integrationData['baseApiUrl'] . '/self-validate';
        $result = $this->sendRequest($authHeaders, $url, 'POST', $requestParams);

        return $result;
    }

    /**
     * Authorization headers calculation.
     * @param array $requestParams [optional]
     * @return array
     */
    protected function getAuthHeaders($requestParams = [])
    {
        $authHeaders = [
            'X-Merchant-Id' => $this->integrationData['merchantId'],
            'X-Timestamp' => time(),
            'X-Nonce' => md5(uniqid(mt_rand(), true)),
        ];

        $mergedParams = array_merge($requestParams, $authHeaders);

        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);

        $authHeaders['X-Sign'] = hash_hmac('sha1', $hashString, $this->integrationData['merchantKey']);

        return $authHeaders;
    }

    /**
     * Send request to Provider.
     * @param array $authHeaders
     * @param string $url
     * @param string $method [optional]
     * @param array $postParams [optional]
     * @return array
     */
    protected function sendRequest($authHeaders, $url, $method = 'GET', $postParams = [])
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 7);

        $headers = [];
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            if (!empty($postParams)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postParams));
                $headers = [
                    'Content-Type: application/x-www-form-urlencoded',
                ];
            }
        }

        foreach ($authHeaders as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $json = curl_exec($curl);
        $result = json_decode($json, true);

        return $result;
    }
}
