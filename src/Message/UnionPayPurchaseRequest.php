<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact UnionPay Purchase Request.
 */
class UnionPayPurchaseRequest extends DirectPostAbstractRequest
{
    /**
     * @var string
     */
    public $txnType = '0';

    /**
     * @return mixed
     */
    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'transactionId');

        $data = $this->getBaseData();

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function sendData($data)
    {
        $redirectUrl = $this->getEndpoint() . '?' . http_build_query($data);

        return $this->response = new UnionPayPurchaseResponse($this, $data, $redirectUrl);
    }
}
