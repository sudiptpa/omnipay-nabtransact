<?php

namespace Omnipay\NABTransact\Transport;

use RuntimeException;

final class CurlTransport implements TransportInterface
{
    /**
     * @param string               $method
     * @param string               $url
     * @param array<string,string> $headers
     * @param string               $body
     * @param int                  $timeoutSeconds
     *
     * @return TransportResponse
     */
    public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('cURL extension is required for CurlTransport.');
        }

        $handle = curl_init();

        if ($handle === false) {
            throw new RuntimeException('Unable to initialize cURL transport.');
        }

        $formattedHeaders = [];
        foreach ($headers as $name => $value) {
            $formattedHeaders[] = $name.': '.$value;
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_TIMEOUT => max(1, (int) $timeoutSeconds),
            CURLOPT_HTTPHEADER => $formattedHeaders,
            CURLOPT_POSTFIELDS => $body,
        ];

        curl_setopt_array($handle, $options);

        $responseBody = curl_exec($handle);

        if ($responseBody === false) {
            $message = curl_error($handle);
            curl_close($handle);
            throw new RuntimeException('cURL transport request failed: '.$message);
        }

        $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        return new TransportResponse($statusCode, (string) $responseBody);
    }
}
