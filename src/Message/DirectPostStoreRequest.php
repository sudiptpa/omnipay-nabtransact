<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Store Only Request.
 */
class DirectPostStoreRequest extends DirectPostAuthorizeRequest
{
    /**
     * @var string
     */
    public $txnType = '8';

    public function getStoreType()
    {
        return $this->getParameter('storeType');
    }

    public function setStoreType($value)
    {
        return $this->setParameter('storeType', $value);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('returnUrl', 'card');

        if (!$this->getAmount()) {
            $this->setAmount('0.00');
        }

        $data = parent::getData();
        $data['EPS_STORE'] = 'true';
        $data['EPS_STORETYPE'] = (string) ($this->getStoreType() ?: 'TOKEN');
        $data['EPS_FINGERPRINT'] = $this->generateFingerprint($data);

        return $data;
    }

    /**
     * @param array $data
     */
    public function generateFingerprint(array $data)
    {
        if ((string) $this->txnType !== '8' || !isset($data['EPS_STORETYPE'])) {
            return parent::generateFingerprint($data);
        }

        $hashable = [
            $data['EPS_MERCHANT'],
            $this->getTransactionPassword(),
            $data['EPS_TXNTYPE'],
            $data['EPS_STORETYPE'],
            $data['EPS_REFERENCEID'],
            $data['EPS_TIMESTAMP'],
        ];

        return hash_hmac('sha256', implode('|', $hashable), $this->getTransactionPassword());
    }
}
