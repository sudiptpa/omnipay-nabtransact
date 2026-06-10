<?php

namespace Omnipay\NABTransact\Transport;

use Nyholm\Psr7\Response;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\NABTransact\Tests\Support\TestCase;

class OmnipayHttpClientTransportTest extends TestCase
{
    public function testSendForwardsRequestAndMapsPsrResponse()
    {
        $capture = (object) ['method' => null, 'uri' => null, 'headers' => [], 'body' => null];

        $client = new class ($capture) implements ClientInterface {
            private $capture;

            public function __construct($capture)
            {
                $this->capture = $capture;
            }

            public function request($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
            {
                $this->capture->method = $method;
                $this->capture->uri = $uri;
                $this->capture->headers = $headers;
                $this->capture->body = $body;

                return new Response(201, [], '{"orderId":"ORDER-1"}');
            }
        };

        $transport = new OmnipayHttpClientTransport($client);

        $response = $transport->send('post', 'https://example.test/orders', ['X-Test' => 'yes'], '{"k":"v"}', 15);

        $this->assertSame('POST', $capture->method);
        $this->assertSame('https://example.test/orders', $capture->uri);
        $this->assertSame(['X-Test' => 'yes'], $capture->headers);
        $this->assertSame('{"k":"v"}', $capture->body);

        $this->assertInstanceOf(TransportResponse::class, $response);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('{"orderId":"ORDER-1"}', $response->getBody());
    }

    public function testSendCastsNonStandardStatusAndEmptyBody()
    {
        $client = new class () implements ClientInterface {
            public function request($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
            {
                return new Response(204, [], '');
            }
        };

        $response = (new OmnipayHttpClientTransport($client))->send('GET', 'https://example.test/ping');

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', $response->getBody());
    }
}
