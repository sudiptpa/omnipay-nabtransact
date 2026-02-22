<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\NABTransact\Transport\OmnipayHttpClientTransport;

/**
 * DirectPost EMV 3DS order creation request.
 */
class EMV3DSOrderRequest extends AbstractRequest
{
    /**
     * @var string
     */
    public $testEndpoint = 'https://demo.transact.nab.com.au/services/order-management/v2/payments/orders';

    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/services/order-management/v2/payments/orders';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('merchantId', 'transactionPassword', 'amount', 'currency', 'clientIp', 'transactionReference');

        return [
            'amount' => $this->getAmountInteger(),
            'currency' => $this->getCurrency(),
            'ip' => $this->getClientIp(),
            'merchantId' => $this->getMerchantId(),
            'merchantOrderReference' => $this->getTransactionReference(),
            'orderType' => $this->getOrderType(),
            'intents' => $this->getIntents(),
        ];
    }

    /**
     * @return string
     */
    public function getOrderType()
    {
        return $this->getParameter('orderType') ?: 'PAYMENT';
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function setOrderType($value)
    {
        return $this->setParameter('orderType', $value);
    }

    /**
     * @return array
     */
    public function getIntents()
    {
        $intents = $this->getParameter('intents');

        if (!is_array($intents) || empty($intents)) {
            return ['THREED_SECURE'];
        }

        return array_values($intents);
    }

    /**
     * @param array $value
     *
     * @return mixed
     */
    public function setIntents(array $value)
    {
        return $this->setParameter('intents', $value);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return EMV3DSOrderResponse
     */
    public function sendData($data)
    {
        $transport = $this->getTransport();
        if ($transport === null) {
            $transport = new OmnipayHttpClientTransport($this->httpClient);
        }

        $payload = json_encode($data);

        if ($payload === false) {
            throw new InvalidRequestException('Unable to encode EMV 3DS order payload.');
        }

        $authorization = base64_encode($this->getMerchantId().':'.$this->getTransactionPassword());

        $response = $transport->send(
            'POST',
            $this->getEndpoint(),
            [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Basic '.$authorization,
            ],
            $payload,
            $this->getTimeoutSeconds()
        );

        $parsed = json_decode((string) $response->getBody(), true);
        if (!is_array($parsed)) {
            $parsed = [];
        }

        $parsed['http_status_code'] = $response->getStatusCode();
        $parsed['raw'] = (string) $response->getBody();

        return $this->response = new EMV3DSOrderResponse($this, $parsed);
    }
}
