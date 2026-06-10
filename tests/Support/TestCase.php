<?php

declare(strict_types=1);

namespace Omnipay\NABTransact\Tests\Support;

use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\RequestInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Symfony\Component\HttpFoundation\Request;

class TestCase extends PHPUnitTestCase
{
    private MockHttpClient $httpClient;

    private Request $httpRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new MockHttpClient();
        $this->httpRequest = Request::create('/');
    }

    protected function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    protected function getHttpRequest(): Request
    {
        return $this->httpRequest;
    }

    protected function getMockRequest(): RequestInterface
    {
        return $this->createMock(RequestInterface::class);
    }

    protected function queueFixtureResponse(string $filename, int $statusCode = 200, array $headers = []): void
    {
        $body = $this->fixtureContents($filename);

        $this->httpClient->queueResponse($body, $statusCode, $headers);
    }

    protected function fixtureContents(string $filename): string
    {
        $path = dirname(__DIR__) . '/Mock/' . $filename;

        if (!is_file($path)) {
            $this->fail('Missing mock HTTP fixture: ' . $filename);
        }

        $body = file_get_contents($path);
        if ($body === false) {
            $this->fail('Unable to read mock HTTP fixture: ' . $filename);
        }

        // Legacy fixture files may contain an HTTP status line and headers.
        // Keep only the actual payload body for request parsers.
        if (preg_match('/^HTTP\\/\\d(?:\\.\\d)?\\s+\\d+/', $body) === 1) {
            $parts = preg_split("/\\r?\\n\\r?\\n/", $body, 2);
            if (is_array($parts) && isset($parts[1])) {
                $body = $parts[1];
            }
        }

        return $body;
    }

    protected function getValidCard(): array
    {
        return [
            'firstName'   => 'Example',
            'lastName'    => 'User',
            'number'      => '4444333322221111',
            'expiryMonth' => '12',
            'expiryYear'  => '2030',
            'cvv'         => '123',
            'address1'    => '123 Test Street',
            'city'        => 'Billstown',
            'postcode'    => '12345',
            'country'     => 'US',
        ];
    }
}
