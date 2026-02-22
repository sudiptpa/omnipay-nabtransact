<?php

namespace Omnipay\NABTransact;

use Omnipay\NABTransact\Tests\Support\GatewayTestCase;
use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;

class DirectPostGatewayTest extends GatewayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new DirectPostGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('XYZ0010');
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostAuthorizeRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompleteAuthorize()
    {
        $request = $this->gateway->completeAuthorize(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostPurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostCompletePurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testRiskManagementAliases()
    {
        $this->gateway->setRiskManagement(true);

        $this->assertTrue((bool) $this->gateway->getRiskManagement());
        $this->assertTrue((bool) $this->gateway->getHasRiskManagementEnabled());
    }

    public function testWebhookFactory()
    {
        $request = $this->gateway->webhook(['merchant' => 'XYZ0010']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostWebhookRequest::class, $request);
    }

    public function testStore()
    {
        $request = $this->gateway->store(['amount' => '0.00', 'returnUrl' => 'https://example.com/return']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostStoreRequest::class, $request);
        $this->assertSame('0.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostCaptureRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostRefundRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testVoid()
    {
        $request = $this->gateway->void(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostReversalRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCreateEmv3dsOrder()
    {
        $request = $this->gateway->createEMV3DSOrder(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\EMV3DSOrderRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testSetTransportAndTimeout()
    {
        $transport = new class() implements TransportInterface {
            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                return new TransportResponse(200, '{}');
            }
        };

        $this->gateway->setTransport($transport);
        $this->gateway->setTimeoutSeconds(12);

        $this->assertSame($transport, $this->gateway->getTransport());
        $this->assertSame(12, $this->gateway->getTimeoutSeconds());
    }
}
