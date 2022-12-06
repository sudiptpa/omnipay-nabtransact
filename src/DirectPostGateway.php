<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;

/**
 * NABTransact Direct Post Gateway.
 */
class DirectPostGateway extends AbstractGateway
{
    public function getName()
    {
        return 'NABTransact Direct Post';
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

    public function getHasEMV3DSEnabled()
    {
        return $this->getParameter('hasEMV3DSEnabled');
    }

    public function setHasEMV3DSEnabled($value)
    {
        return $this->setParameter('hasEMV3DSEnabled', $value);
    }

    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostAuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }
}
