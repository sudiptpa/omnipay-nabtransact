<?php

declare(strict_types=1);

namespace Omnipay\NABTransact\Tests\Support;

use Omnipay\Common\Http\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class MockHttpClient implements ClientInterface
{
    /** @var array<int, ResponseInterface> */
    private array $responses = [];

    /** @var array<int, array<string, mixed>> */
    private array $requests = [];

    /**
     * @param int                  $statusCode
     * @param array<string,string> $headers
     */
    public function queueResponse(string $body, int $statusCode = 200, array $headers = []): void
    {
        $this->responses[] = new MockHttpResponse($statusCode, $headers, $body);
    }

    public function queueRawResponse(ResponseInterface $response): void
    {
        $this->responses[] = $response;
    }

    public function getRequests(): array
    {
        return $this->requests;
    }

    /**
     * {@inheritDoc}
     */
    public function request($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1'): ResponseInterface
    {
        $this->requests[] = [
            'method'          => (string) $method,
            'uri'             => (string) $uri,
            'headers'         => $headers,
            'body'            => $body,
            'protocolVersion' => (string) $protocolVersion,
        ];

        if (empty($this->responses)) {
            throw new RuntimeException('No queued mock HTTP response for ' . $method . ' ' . $uri);
        }

        $response = array_shift($this->responses);

        if (!$response instanceof ResponseInterface) {
            throw new RuntimeException('Queued mock HTTP response is invalid.');
        }

        return $response;
    }
}
