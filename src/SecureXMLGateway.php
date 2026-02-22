<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;
use Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest;
use Omnipay\NABTransact\Message\SecureXMLCaptureRequest;
use Omnipay\NABTransact\Message\SecureXMLEchoTestRequest;
use Omnipay\NABTransact\Message\SecureXMLPurchaseRequest;
use Omnipay\NABTransact\Message\SecureXMLRefundRequest;
use Omnipay\NABTransact\Message\SecureXMLRiskPurchaseRequest;
use Omnipay\NABTransact\Transport\TransportInterface;

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

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param $value
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string
     */
    public function getRiskManagement()
    {
        return $this->getParameter('riskManagement');
    }

    /**
     * @param $value
     */
    public function setRiskManagement($value)
    {
        return $this->setParameter('riskManagement', $value);
    }

    /**
     * @return string
     */
    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    /**
     * @param $value
     */
    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    /**
     * Optional custom transport for SecureXML requests.
     *
     * @param TransportInterface $value
     *
     * @return mixed
     */
    public function setTransport(TransportInterface $value)
    {
        return $this->setParameter('transport', $value);
    }

    /**
     * @return TransportInterface|null
     */
    public function getTransport()
    {
        return $this->getParameter('transport');
    }

    /**
     * Optional timeout in seconds for outbound verification calls.
     *
     * @param int $value
     *
     * @return mixed
     */
    public function setTimeoutSeconds($value)
    {
        return $this->setParameter('timeoutSeconds', (int) $value);
    }

    /**
     * @return int|null
     */
    public function getTimeoutSeconds()
    {
        $timeout = $this->getParameter('timeoutSeconds');

        if ($timeout === null) {
            return null;
        }

        return (int) $timeout;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        $request = $this->createRequest(SecureXMLAuthorizeRequest::class, $parameters);
        /** @var SecureXMLAuthorizeRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLCaptureRequest
     */
    public function capture(array $parameters = [])
    {
        $request = $this->createRequest(SecureXMLCaptureRequest::class, $parameters);
        /** @var SecureXMLCaptureRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLPurchaseRequest|\Omnipay\NABTransact\Message\SecureXMLRiskPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        if ($this->getRiskManagement()) {
            $request = $this->createRequest(SecureXMLRiskPurchaseRequest::class, $parameters);
            /** @var SecureXMLRiskPurchaseRequest $request */

            return $request;
        }

        $request = $this->createRequest(SecureXMLPurchaseRequest::class, $parameters);
        /** @var SecureXMLPurchaseRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLRefundRequest
     */
    public function refund(array $parameters = [])
    {
        $request = $this->createRequest(SecureXMLRefundRequest::class, $parameters);
        /** @var SecureXMLRefundRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\SecureXMLEchoTestRequest
     */
    public function echoTest(array $parameters = [])
    {
        $request = $this->createRequest(SecureXMLEchoTestRequest::class, $parameters);
        /** @var SecureXMLEchoTestRequest $request */

        return $request;
    }
}
