<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;
use Omnipay\NABTransact\Transport\TransportInterface;

/**
 * NABTransact Direct Post Gateway.
 *
 * @link https://demo.transact.nab.com.au/nabtransact/downloadDocs.nab?nav=3-4
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

    public function getHasEMV3DSEnabled()
    {
        return $this->getParameter('hasEMV3DSEnabled');
    }

    /**
     * @param $value
     */
    public function setHasEMV3DSEnabled($value)
    {
        return $this->setParameter('hasEMV3DSEnabled', $value);
    }

    public function getHasRiskManagementEnabled()
    {
        return $this->getParameter('hasRiskManagementEnabled');
    }

    public function setHasRiskManagementEnabled($value)
    {
        return $this->setParameter('hasRiskManagementEnabled', $value);
    }

    /**
     * Optional custom transport for server-to-server DirectPost calls.
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
     * Optional timeout in seconds for server-to-server DirectPost calls.
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
     * Backward-friendly alias.
     */
    public function getRiskManagement()
    {
        return $this->getHasRiskManagementEnabled();
    }

    /**
     * Backward-friendly alias.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function setRiskManagement($value)
    {
        return $this->setHasRiskManagementEnabled($value);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostAuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest
     */
    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest', $parameters);
    }

    /**
     * Complete preauth and capture funds using DirectPost server-to-server flow.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCaptureRequest
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostCaptureRequest', $parameters);
    }

    /**
     * Refund a DirectPost transaction using server-to-server flow.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostRefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostRefundRequest', $parameters);
    }

    /**
     * Void/reverse a DirectPost transaction using server-to-server flow.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostReversalRequest
     */
    public function void(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostReversalRequest', $parameters);
    }

    /**
     * Create an EMV 3DS order (order management API).
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\EMV3DSOrderRequest
     */
    public function createEMV3DSOrder(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\EMV3DSOrderRequest', $parameters);
    }

    /**
     * Store card/token details without charging a real amount.
     *
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostStoreRequest
     */
    public function store(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\NABTransact\Message\DirectPostStoreRequest', $parameters);
    }

    /**
     * Convenience factory for webhook fingerprint verification payloads.
     *
     * @param array $data
     *
     * @return \Omnipay\NABTransact\Message\DirectPostWebhookRequest
     */
    public function webhook(array $data = [])
    {
        return new \Omnipay\NABTransact\Message\DirectPostWebhookRequest($data);
    }
}
