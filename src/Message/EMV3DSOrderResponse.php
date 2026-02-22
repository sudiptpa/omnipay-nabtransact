<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * DirectPost EMV 3DS order creation response.
 */
class EMV3DSOrderResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        $statusCode = $this->getHttpStatusCode();

        if ($statusCode === null) {
            return false;
        }

        return $statusCode >= 200 && $statusCode <= 399;
    }

    /**
     * @return int|null
     */
    public function getHttpStatusCode()
    {
        if (isset($this->data['http_status_code'])) {
            return (int) $this->data['http_status_code'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getOrderId()
    {
        if (isset($this->data['orderId'])) {
            return $this->data['orderId'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getSimpleToken()
    {
        if (isset($this->data['simpleToken'])) {
            return $this->data['simpleToken'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getOrderToken()
    {
        if (isset($this->data['orderToken'])) {
            return $this->data['orderToken'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getProviderClientId()
    {
        if (isset($this->data['threedSecure']['providerClientId'])) {
            return $this->data['threedSecure']['providerClientId'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getSessionId()
    {
        if (isset($this->data['threedSecure']['sessionId'])) {
            return $this->data['threedSecure']['sessionId'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        foreach (['message', 'error', 'description'] as $key) {
            if (isset($this->data[$key])) {
                return (string) $this->data[$key];
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getRawResponse()
    {
        if (isset($this->data['raw'])) {
            return (string) $this->data['raw'];
        }

        return null;
    }
}
