<?php

namespace Omnipay\NABTransact\Transport;

use Omnipay\Common\Http\ClientInterface;

final class OmnipayHttpClientTransport implements TransportInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

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
        $response = $this->httpClient->request(strtoupper($method), $url, $headers, $body);

        return new TransportResponse(
            (int) $response->getStatusCode(),
            (string) $response->getBody()
        );
    }
}
