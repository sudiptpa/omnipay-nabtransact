<?php

namespace Omnipay\NABTransact;

use Omnipay\NABTransact\Tests\Support\GatewayTestCase;

class HostedPaymentGatewayTest extends GatewayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new HostedPaymentGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('XYZ0010');
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00', 'transactionId' => 'ORDER-100']);

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase();

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest::class, $request);
    }
}
