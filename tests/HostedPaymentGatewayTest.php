<?php

namespace Omnipay\NABTransact;

use Omnipay\Tests\GatewayTestCase;

class HostedPaymentGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new HostedPaymentGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('XYZ0010');
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(['amount' => '10.00', 'transactionId' => 'ORDER-100']);

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\HostedPaymentPurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase();

        $this->assertInstanceOf('\Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseRequest', $request);
    }
}
