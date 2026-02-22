<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;
use Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest;
use Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest;

/**
 * HostedPayment Gateway.
 */
class HostedPaymentGateway extends AbstractGateway
{
    public function getDefaultParameters()
    {
        return [
            'merchantId'        => '',
            'paymentAlertEmail' => '',
            'returnUrlText'     => '',
            'testMode'          => false,
        ];
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        $request = $this->createRequest(HostedPaymentCompletePurchaseRequest::class, $parameters);
        /** @var HostedPaymentCompletePurchaseRequest $request */

        return $request;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function getName()
    {
        return 'NAB Hosted Payment';
    }

    /**
     * @return string
     */
    public function getPaymentAlertEmail()
    {
        return $this->getParameter('paymentAlertEmail');
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function setPaymentAlertEmail($value)
    {
        return $this->setParameter('paymentAlertEmail', $value);
    }

    /**
     * @return string
     */
    public function getReturnUrlText()
    {
        return $this->getParameter('returnUrlText');
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function setReturnUrlText($value)
    {
        return $this->setParameter('returnUrlText', $value);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        $request = $this->createRequest(HostedPaymentPurchaseRequest::class, $parameters);
        /** @var HostedPaymentPurchaseRequest $request */

        return $request;
    }

    /**
     * @param $value
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }
}
