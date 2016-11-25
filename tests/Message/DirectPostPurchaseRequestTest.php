<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DirectPostPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            array(
                'merchantId' => 'foo',
                'transactionPassword' => 'bar',
                'amount' => '12.00',
                'returnUrl' => 'https://www.example.com/return',
                'card' => array(
                    'number' => '4444333322221111',
                    'expiryMonth' => '6',
                    'expiryYear' => '2020',
                    'cvv' => '123',
                ),
            )
        );
    }

    public function testFingerprint()
    {
        $data = $this->request->getData();
        $data['EPS_TIMESTAMP'] = '20130416123332';

        $this->assertSame('652856e75b04c5916a41082e04c9390961497f65', $this->request->generateFingerprint($data));
    }

    public function testSend()
    {
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\DirectPostAuthorizeResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());

        $this->assertSame('https://transact.nab.com.au/live/directpostv2/authorise', $response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());

        $data = $response->getData();
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
        $this->assertSame('0', $data['EPS_TXNTYPE']);
    }
}
