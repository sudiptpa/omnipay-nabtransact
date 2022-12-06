<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact SecureXML Purchase Request.
 */
class SecureXMLPurchaseRequest extends SecureXMLAbstractRequest
{
    protected $txnType = 0;

    protected $requiredFields = ['amount', 'card', 'transactionId'];

    public function getData()
    {
        return $this->getBasePaymentXMLWithCard();
    }
}
