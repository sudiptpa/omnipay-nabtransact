<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Authorize Request.
 */
class DirectPostAuthorizeRequest extends DirectPostAbstractRequest
{
    /**
     * @var string
     */
    public $txnType = '1';

    /**
     * @return mixed
     */
    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'card');

        $data = array();
        $data['EPS_MERCHANT'] = $this->getMerchantId();
        $data['EPS_TXNTYPE'] = $this->txnType;
        $data['EPS_IP'] = $this->getClientIp();
        $data['EPS_AMOUNT'] = $this->getAmount();
        $data['EPS_REFERENCEID'] = $this->getTransactionId();
        $data['EPS_TIMESTAMP'] = gmdate('YmdHis');
        $data['EPS_FINGERPRINT'] = $this->generateFingerprint($data);
        $data['EPS_RESULTURL'] = $this->getReturnUrl();
        $data['EPS_CALLBACKURL'] = $this->getNotifyUrl() ?: $this->getReturnUrl();
        $data['EPS_REDIRECT'] = 'TRUE';
        $data['EPS_CURRENCY'] = $this->getCurrency();

        $data = array_replace($data, $this->getCardData());

        return $data;
    }

    /**
     * @param array $data
     */
    public function generateFingerprint(array $data)
    {
        $hash = implode(
            '|',
            array(
                $data['EPS_MERCHANT'],
                $this->getTransactionPassword(),
                $data['EPS_TXNTYPE'],
                $data['EPS_REFERENCEID'],
                $data['EPS_AMOUNT'],
                $data['EPS_TIMESTAMP'],
            )
        );

        return sha1($hash);
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function sendData($data)
    {
        return $this->response = new DirectPostAuthorizeResponse($this, $data, $this->getEndpoint());
    }

    /**
     * @return mixed
     */
    protected function getCardData()
    {
        $this->getCard()->validate();

        $data = array();

        $data['EPS_CARDNUMBER'] = $this->getCard()->getNumber();
        $data['EPS_EXPIRYMONTH'] = $this->getCard()->getExpiryMonth();
        $data['EPS_EXPIRYYEAR'] = $this->getCard()->getExpiryYear();
        $data['EPS_CCV'] = $this->getCard()->getCvv();

        return $data;
    }
}
