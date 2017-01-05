<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * HostedPayment Gateway
 */
class HostedPaymentGateway extends AbstractGateway
{
    /**
     * @param array $parameters
     * @return mixed
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest', $parameters);
    }

    public function getName()
    {
        return 'NAB Hosted Payment';
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest', $parameters);
    }
}
