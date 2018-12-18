<?php
namespace providerBundle\responses;

class SuccessResponse
{
    protected $balance;
    protected $transactionId;

    /**
     * SuccessResponse constructor.
     *
     * @param double $balance
     * @param string $transactionId {optional}
     */
    public function __construct($balance, $transactionId = null)
    {
        $this->balance = $balance;
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function render()
    {
        return json_encode(array_filter([
            'balance'=>$this->balance,
            'transaction_id'=>$this->transactionId,
        ]));
    }
}
