<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;

class UnionPayCompletePurchaseRequestTest extends TestCase
{
    public function testUnionPayCompletePurchaseSuccess()
    {
        $data = [
            'restext' => 'Approved',
            'rescode' => '00',
            'summarycode' => '1',
            'txnid' => '12345',
        ];

        $response = new UnionPayCompletePurchaseResponse($this->getMockRequest(), $data);

        $this->assertInstanceOf(Omnipay\NABTransact\Message\UnionPayCompletePurchaseResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('00', $response->getCode());
        $this->assertTrue($response->summaryCode());
    }

    public function testUnionPayCompletePurchaseFailure()
    {
        $data = [
            'restext' => 'Error',
            'txnid' => '12345',
            'summarycode' => '3',
            'rescode' => '06',
        ];

        $response = new UnionPayCompletePurchaseResponse($this->getMockRequest(), $data);

        $this->assertInstanceOf(Omnipay\NABTransact\Message\UnionPayCompletePurchaseResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('12345', $response->getTransactionReference());
        $this->assertNotSame('Approved', $response->getMessage());
        $this->assertSame('06', $response->getCode());
    }
}
