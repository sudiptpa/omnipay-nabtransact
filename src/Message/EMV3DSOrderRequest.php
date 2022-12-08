<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact EMV 3DS Order Request.
 */
class EMV3DSOrderRequest extends DirectPostAuthorizeRequest
{
    public $testEndpoint = 'https://demo.transact.nab.com.au/services/order-management/v2/payments/orders';

    public $liveEndpoint = 'https://transact.nab.com.au/services/order-management/v2/payments/orders';

    public function getData()
    {
        $this->validate('amount', 'currency', 'clientIp', 'merchantOrderReference');

        return [
            'amount'                 => floor($this->getAmount() * 100),
            'currency'               => $this->getCurrency(),
            'ip'                     => $this->getClientIp(),
            'merchantId'             => $this->getMerchantId(),
            'merchantOrderReference' => $this->getTransactionReference(),
            'orderType'              => 'PAYMENT',
            'intents'                => [
                'THREED_SECURE',
            ],
        ];
    }

    public function sendData($data)
    {
        $params = [
            'headers' => [
                'Accept'        => '*/*',
                'Content-Type'  => 'application/json; charset=UTF-8',
                'Authorization' => 'Basic '.base64_encode("{$this->getMerchantId()}:{$this->getTransactionPassword()}"),
            ],
            'body' => json_encode($data),
        ];

        $response = $this->httpClient->post($this->getEndpoint(), $params)->send();

        return $this->response = new EMV3DSOrderResponse($this, $response->json());
    }
}
