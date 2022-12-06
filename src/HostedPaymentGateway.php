<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * HostedPayment Gateway.
 */
class HostedPaymentGateway extends AbstractGateway
{
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest', $parameters);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function getName()
    {
        return 'NAB Hosted Payment';
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest', $parameters);
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }
}
