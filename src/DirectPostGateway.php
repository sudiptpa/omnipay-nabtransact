<?php

namespace Omnipay\NABTransact;

use Omnipay\Common\AbstractGateway;
use Omnipay\NABTransact\Message\DirectPostAuthorizeRequest;
use Omnipay\NABTransact\Message\DirectPostCaptureRequest;
use Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest;
use Omnipay\NABTransact\Message\DirectPostPurchaseRequest;
use Omnipay\NABTransact\Message\DirectPostRefundRequest;
use Omnipay\NABTransact\Message\DirectPostReversalRequest;
use Omnipay\NABTransact\Message\DirectPostStoreRequest;
use Omnipay\NABTransact\Message\DirectPostWebhookRequest;
use Omnipay\NABTransact\Message\EMV3DSOrderRequest;
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
        $request = $this->createRequest(DirectPostAuthorizeRequest::class, $parameters);
        /** @var DirectPostAuthorizeRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest
     */
    public function completeAuthorize(array $parameters = [])
    {
        $request = $this->createRequest(DirectPostCompletePurchaseRequest::class, $parameters);
        /** @var DirectPostCompletePurchaseRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function purchase(array $parameters = [])
    {
        $request = $this->createRequest(DirectPostPurchaseRequest::class, $parameters);
        /** @var DirectPostPurchaseRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        $request = $this->createRequest(DirectPostCompletePurchaseRequest::class, $parameters);
        /** @var DirectPostCompletePurchaseRequest $request */

        return $request;
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
        $request = $this->createRequest(DirectPostCaptureRequest::class, $parameters);
        /** @var DirectPostCaptureRequest $request */

        return $request;
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
        $request = $this->createRequest(DirectPostRefundRequest::class, $parameters);
        /** @var DirectPostRefundRequest $request */

        return $request;
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
        $request = $this->createRequest(DirectPostReversalRequest::class, $parameters);
        /** @var DirectPostReversalRequest $request */

        return $request;
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
        $request = $this->createRequest(EMV3DSOrderRequest::class, $parameters);
        /** @var EMV3DSOrderRequest $request */

        return $request;
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
        $request = $this->createRequest(DirectPostStoreRequest::class, $parameters);
        /** @var DirectPostStoreRequest $request */

        return $request;
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
        return new DirectPostWebhookRequest($data);
    }
}
