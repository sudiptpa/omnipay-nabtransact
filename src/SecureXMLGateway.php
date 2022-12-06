<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * NABTransact Secure XML Gateway.
 */
class SecureXMLGateway extends AbstractGateway
{
    public function getName()
    {
        return 'NAB Transact XML';
    }

    public function getDefaultParameters()
    {
        return [
            'merchantId'          => '',
            'transactionPassword' => '',
            'testMode'            => false,
        ];
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest', $parameters);
    }

    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLCaptureRequest', $parameters);
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLPurchaseRequest', $parameters);
    }

    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLRefundRequest', $parameters);
    }

    public function echoTest(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\SecureXMLEchoTestRequest', $parameters);
    }
}
