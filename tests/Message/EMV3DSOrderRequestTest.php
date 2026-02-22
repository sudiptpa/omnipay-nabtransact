<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;
use Omnipay\Tests\TestCase;

class EMV3DSOrderRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new EMV3DSOrderRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount' => '12.00',
            'currency' => 'AUD',
            'clientIp' => '1.2.3.4',
            'transactionReference' => 'EMV-ORDER-100',
        ]);
    }

    public function testGetDataBuildsDefaultPayload()
    {
        $data = $this->request->getData();

        $this->assertSame(1200, $data['amount']);
        $this->assertSame('AUD', $data['currency']);
        $this->assertSame('1.2.3.4', $data['ip']);
        $this->assertSame('XYZ0010', $data['merchantId']);
        $this->assertSame('EMV-ORDER-100', $data['merchantOrderReference']);
        $this->assertSame('PAYMENT', $data['orderType']);
        $this->assertSame(['THREED_SECURE'], $data['intents']);
    }

    public function testSendUsesTransportAndParsesResponse()
    {
        $capture = (object) ['method' => null, 'url' => null, 'headers' => [], 'body' => null, 'timeout' => null];

        $request = $this->request;
        $request->setTimeoutSeconds(15);
        $request->setTransport(new class($capture) implements TransportInterface {
            private $capture;

            public function __construct($capture)
            {
                $this->capture = $capture;
            }

            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                $this->capture->method = $method;
                $this->capture->url = $url;
                $this->capture->headers = $headers;
                $this->capture->body = $body;
                $this->capture->timeout = $timeoutSeconds;

                return new TransportResponse(201, '{"orderId":"ORDER-1","simpleToken":"S-1","orderToken":"O-1","threedSecure":{"providerClientId":"P-1","sessionId":"SESS-1"}}');
            }
        });

        $response = $request->send();

        $this->assertSame('POST', $capture->method);
        $this->assertSame('https://transact.nab.com.au/services/order-management/v2/payments/orders', $capture->url);
        $this->assertArrayHasKey('Authorization', $capture->headers);
        $this->assertStringContainsString('"merchantOrderReference":"EMV-ORDER-100"', $capture->body);
        $this->assertSame(15, $capture->timeout);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('ORDER-1', $response->getOrderId());
        $this->assertSame('S-1', $response->getSimpleToken());
        $this->assertSame('O-1', $response->getOrderToken());
        $this->assertSame('P-1', $response->getProviderClientId());
        $this->assertSame('SESS-1', $response->getSessionId());
    }

    public function testSendHandlesInvalidJsonResponse()
    {
        $request = $this->request;
        $request->setTransport(new class implements TransportInterface {
            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                return new TransportResponse(500, 'Internal Error');
            }
        });

        $response = $request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Internal Error', $response->getRawResponse());
    }
}
