<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Get EMV 3DS Order Request.
 */
class GetEMV3DSOrderRequest extends AbstractRequest
{
    public $testEndpoint = 'https://demo.transact.nab.com.au/services/order-management/v2/payments/orders';

    public $liveEndpoint = 'https://transact.nab.com.au/services/order-management/v2/payments/orders';

    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    public function getData()
    {
        $this->validate('orderId');

        return [];
    }

    public function sendData($data)
    {
        $authorizationHeader = base64_encode("{$this->getMerchantId()}:{$this->getTransactionPassword()}");

        $headers = [
            'Content-Type'  => 'application/json; charset=UTF-8',
            'Authorization' => "Basic {$authorizationHeader}",
        ];

        $response = $this->httpClient->get("{$this->getEndpoint()}/{$this->getOrderId()}", $headers)->send();

        $json = $response->json();

        $json['http_status_code'] = $response->getStatusCode();

        return $this->response = new GetEMV3DSOrderResponse($this, $json);
    }
}
