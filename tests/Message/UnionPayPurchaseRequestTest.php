<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class UnionPayPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new UnionPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            [
                'merchantId'          => 'XYZ0010',
                'transactionPassword' => 'abcd1234',
                'amount'              => '12.00',
                'returnUrl'           => 'https://www.example.com/return',
                'transactionId'       => 'GHJGG76756556',
                'card'                => [
                    'number'          => '4444333322221111',
                    'expiryMonth'     => '06',
                    'expiryYear'      => '2039',
                    'cvv'             => '123',
                    'billingPostcode' => '1234',
                ],
            ]
        );
    }

    public function testFingerprint()
    {
        $data = $this->request->getData();
        $data['EPS_TIMESTAMP'] = '20161125123332';

        $this->assertSame(
            'a44c4e30e838b34bfbcbe89495f50c1527c1c2c4229960b8d9de159fb5a99f20',
            $this->request->generateFingerprint($data)
        );
    }

    public function testPurchase()
    {
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\UnionPayPurchaseResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());

        $this->assertStringStartsWith(
            'https://transact.nab.com.au/live/directpostv2/authorise',
            $response->getRedirectUrl()
        );
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertArrayHasKey('EPS_FINGERPRINT', $response->getData());
    }
}
