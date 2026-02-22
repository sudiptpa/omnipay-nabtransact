<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;

class HostedPaymentPurchaseRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new HostedPaymentPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'        => 'XYZ0010',
            'paymentAlertEmail' => 'merchant@example.com',
            'amount'            => '10.00',
            'currency'          => 'AUD',
            'transactionId'     => 'ORDER-123',
            'returnUrl'         => 'https://example.com/return',
            'notifyUrl'         => 'https://example.com/notify',
            'returnUrlText'     => 'Return',
        ]);
    }

    public function testUsesTestEndpointInTestMode()
    {
        $this->request->setTestMode(true);

        $this->assertSame('https://transact.nab.com.au/test/hpp/payment', $this->request->getEndpoint());
    }

    public function testUsesLiveEndpointInLiveMode()
    {
        $this->request->setTestMode(false);

        $this->assertSame('https://transact.nab.com.au/live/hpp/payment', $this->request->getEndpoint());
    }
}
