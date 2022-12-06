<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Authorize Request.
 */
class DirectPostAuthorizeRequest extends DirectPostAbstractRequest
{
    public $txnType = '1';

    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'card');

        $data = $this->getBaseData();

        $data = array_replace($data, $this->getCardData());

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new DirectPostAuthorizeResponse($this, $data, $this->getEndpoint());
    }

    protected function getCardData()
    {
        $this->getCard()->validate();

        $data = [];

        $data['EPS_CARDNUMBER'] = $this->getCard()->getNumber();
        $data['EPS_EXPIRYMONTH'] = $this->getCard()->getExpiryMonth();
        $data['EPS_EXPIRYYEAR'] = $this->getCard()->getExpiryYear();
        $data['EPS_CCV'] = $this->getCard()->getCvv();

        return $data;
    }
}
