<?php
namespace providerBundle\responses;

use providerBundle\exceptions\InsufficientFundsException;

class ErrorResponse
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * ErrorResponse constructor.
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return string
     */
    public function render()
    {
        return json_encode([
            'error_message'=>$this->exception->getMessage(),
            'error_code'=>$this->exception instanceof InsufficientFundsException ? 'INSUFFICIENT_FUNDS' : 'INTERNAL_ERROR',
        ]);
    }
}
