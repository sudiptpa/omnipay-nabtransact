<?php

namespace Omnipay\NABTransact;

use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;
use Omnipay\NABTransact\Tests\Support\GatewayTestCase;

class SecureXMLGatewayTest extends GatewayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new SecureXMLGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('ABC0001');
    }

    public function testEcho()
    {
        $request = $this->gateway->echoTest(['amount' => '10.00', 'transactionId' => 'Order-YKHU67']);
        $this->assertInstanceOf(\Omnipay\NABTransact\Message\SecureXMLEchoTestRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('Order-YKHU67', $request->getTransactionId());
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\SecureXMLAuthorizeRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture(['amount' => '10.00', 'transactionId' => 'Order-YKHU67']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\SecureXMLCaptureRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('Order-YKHU67', $request->getTransactionId());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\SecureXMLPurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testPurchaseRiskManaged()
    {
        $gateway = clone $this->gateway;
        $gateway->setRiskManagement(true);
        $request = $gateway->purchase(['card' => $this->getValidCard(), 'transactionId' => 'Test1234', 'ip' => '1.1.1.1', 'amount' => '25.00']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\SecureXMLRiskPurchaseRequest::class, $request);
        $this->assertSame('25.00', $request->getAmount());
        $this->assertStringContainsString(
            '<BuyerInfo><ip>1.1.1.1</ip><firstName>Example</firstName><lastName>User</lastName><zipcode>12345</zipcode><town>Billstown</town><billingCountry>US</billingCountry></BuyerInfo>',
            (string) $request->getData()->asXml()
        );
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(['amount' => '10.00', 'transactionId' => 'Order-YKHU67']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\SecureXMLRefundRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('Order-YKHU67', $request->getTransactionId());
    }

    public function testTransportAndTimeoutArePassedToRequest()
    {
        $transport = new class implements TransportInterface {
            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                return new TransportResponse(200, '<NABTransactMessage><Status><statusCode>000</statusCode><statusDescription>Normal</statusDescription></Status><RequestType>Echo</RequestType></NABTransactMessage>');
            }
        };

        $gateway = clone $this->gateway;
        $gateway->setTransport($transport);
        $gateway->setTimeoutSeconds(25);

        $request = $gateway->echoTest();

        $this->assertSame($transport, $request->getTransport());
        $this->assertSame(25, $request->getTimeoutSeconds());
    }
}
