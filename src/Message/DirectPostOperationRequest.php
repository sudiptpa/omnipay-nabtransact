<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Transport\OmnipayHttpClientTransport;
use Omnipay\NABTransact\Transport\TransportResponse;

/**
 * DirectPost server-to-server operation request.
 */
abstract class DirectPostOperationRequest extends DirectPostAbstractRequest
{
    /**
     * @var string
     */
    protected $targetTransactionField = 'EPS_ORIGINALTXNID';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('merchantId', 'transactionPassword', 'amount', 'transactionId', 'transactionReference');

        $data = [
            'EPS_MERCHANT' => $this->getMerchantId(),
            'EPS_TXNTYPE' => $this->resolveTxnType(),
            'EPS_REFERENCEID' => $this->getTransactionId(),
            'EPS_AMOUNT' => $this->getAmount(),
            $this->targetTransactionField => $this->getTransactionReference(),
            'EPS_TIMESTAMP' => gmdate('YmdHis'),
        ];

        if ($currency = $this->getCurrency()) {
            $data['EPS_CURRENCY'] = $currency;
        }

        if ($returnUrl = $this->getReturnUrl()) {
            $data['EPS_RESULTURL'] = $returnUrl;
        }

        $data['EPS_FINGERPRINT'] = $this->generateOperationFingerprint($data);

        return $data;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return string
     */
    protected function generateOperationFingerprint(array $data)
    {
        $hashable = [
            $data['EPS_MERCHANT'],
            $this->getTransactionPassword(),
            $data['EPS_TXNTYPE'],
            $data['EPS_REFERENCEID'],
            $data['EPS_AMOUNT'],
            $data[$this->targetTransactionField],
            $data['EPS_TIMESTAMP'],
        ];

        return hash_hmac('sha256', implode('|', $hashable), $this->getTransactionPassword());
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return DirectPostApiResponse
     */
    public function sendData($data)
    {
        $transport = $this->getTransport();
        if ($transport === null) {
            $transport = new OmnipayHttpClientTransport($this->httpClient);
        }

        $response = $transport->send(
            'POST',
            $this->getEndpoint(),
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            http_build_query($data, '', '&'),
            $this->getTimeoutSeconds()
        );

        $parsed = $this->parseTransportResponse($response);

        return $this->response = new DirectPostApiResponse($this, $parsed);
    }

    /**
     * @return array<string,mixed>
     */
    protected function parseTransportResponse(TransportResponse $response)
    {
        $body = trim((string) $response->getBody());

        $parsed = [
            'http_status_code' => $response->getStatusCode(),
            'raw' => $body,
        ];

        if ($body === '') {
            return $parsed;
        }

        $json = json_decode($body, true);
        if (is_array($json)) {
            return array_merge($parsed, $this->normalizeKeys($json));
        }

        $query = [];
        parse_str($body, $query);
        if (!empty($query)) {
            return array_merge($parsed, $this->normalizeKeys($query));
        }

        if (function_exists('simplexml_load_string')) {
            $xml = @simplexml_load_string($body);
            if ($xml !== false) {
                $xmlData = [];
                foreach ($xml->children() as $node) {
                    $xmlData[strtolower($node->getName())] = (string) $node;
                }

                if (!empty($xmlData)) {
                    return array_merge($parsed, $xmlData);
                }
            }
        }

        return $parsed;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    private function normalizeKeys(array $data)
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[strtolower((string) $key)] = $value;
        }

        return $normalized;
    }
}
