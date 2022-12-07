<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DirectPostAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            [
                'merchantId'          => 'XYZ0010',
                'transactionPassword' => 'abcd1234',
                'amount'              => '12.00',
                'returnUrl'           => 'https://www.abc.com/return',
                'card'                => [
                    'number'          => '4444333322221111',
                    'expiryMonth'     => '6',
                    'expiryYear'      => '2030',
                    'cvv'             => '123',
                    'billingPostcode' => '1234',
                ],
            ]
        );
    }

    public function testFingerprint()
    {
        $data = $this->request->getData();
        $data['EPS_TIMESTAMP'] = '20161126053143';

        $this->assertSame('a748577a6fe6fbbda744a4df9e36595bd1a9bcaefc88f360ea60126c93ffc042', $this->request->generateFingerprint($data));
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
        $this->assertSame('1', $data['EPS_TXNTYPE']);
    }
}
