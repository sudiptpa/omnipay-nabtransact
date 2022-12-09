<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * NABTransact EMV 3DS Order Response.
 */
class EMV3DSOrderResponse extends AbstractResponse
{
    /**
     * @param RequestInterface $request
     * @param $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
    }

    public function isSuccessful()
    {
        $statusCode = $this->getResponseData('http_status_code');

        if (is_null($statusCode)) {
            return false;
        }

        return $statusCode >= 200 && $statusCode <= 399;
    }

    public function getResponseData($key)
    {
        $data = $this->data;

        return isset($data[$key]) ? $data[$key] : null;
    }

    public function getOrderId()
    {
        return $this->getResponseData('orderId');
    }

    public function getSimpleToken()
    {
        return $this->getResponseData('simpleToken');
    }

    public function getOrderToken()
    {
        return $this->getResponseData('orderToken');
    }

    public function getProviderClientId()
    {
        $data = $this->data;

        return isset($data['threedSecure']['providerClientId'])
            ? $data['threedSecure']['providerClientId']
            : null;
    }

    public function getSessionId()
    {
        $data = $this->data;

        return isset($data['threedSecure']['sessionId'])
            ? $data['threedSecure']['sessionId']
            : null;
    }
}
