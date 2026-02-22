<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;

class HostedPaymentCompletePurchaseRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new HostedPaymentCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testReadDataFromQuery()
    {
        $this->getHttpRequest()->query->replace([
            'summarycode' => '1',
            'rescode' => '00',
            'restext' => 'Approved',
            'txnid' => 'TXN-100',
        ]);

        $response = $this->request->send();

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\HostedPaymentCompletePurchaseResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('TXN-100', $response->getTransactionReference());
    }

    public function testReadDataFromRequestBodyWhenQueryMissing()
    {
        $this->getHttpRequest()->request->replace([
            'summarycode' => '3',
            'rescode' => '06',
            'restext' => 'Declined',
            'txnid' => 'TXN-101',
        ]);

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Declined', $response->getMessage());
    }
}
