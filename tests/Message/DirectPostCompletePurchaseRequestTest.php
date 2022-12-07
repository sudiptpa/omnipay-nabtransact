<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostCompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new DirectPostCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGenerateResponseFingerprint()
    {
        $this->request->initialize(
            [
                'amount'              => '465.18',
                'transactionPassword' => 'abcd1234',
            ]
        );

        $data = [
            'timestamp'   => '20161125123332',
            'merchant'    => 'XYZ0010',
            'refid'       => '222',
            'summarycode' => '2',
        ];

        $this->assertSame(
            '56dc3f5dc4473d66138913317e743ea208e205a3be1abdd6515b5c5578a58f38',
            $this->request->generateResponseFingerprint($data)
        );
    }

    public function testSuccess()
    {
        $this->request->initialize(
            [
                'amount'              => '12.00',
                'transactionPassword' => 'abcd1234',
                'transactionId'       => 'ORDER-ZYX8',
            ]
        );

        $this->getHttpRequest()->query->replace(
            [
                'timestamp'            => '20161125130241',
                'callback_status_code' => '-1',
                'fingerprint'          => '796a4f3ac07d04f2336188ffe5a4befeb9fcb6f756061d4e42a30f228cda420e',
                'txnid'                => '271337',
                'merchant'             => 'XYZ0010',
                'restext'              => 'Approved',
                'rescode'              => '00',
                'expirydate'           => '20161126',
                'settdate'             => '20161126',
                'refid'                => 'ORDER-ZYX8',
                'pan'                  => '444433...111',
                'summarycode'          => '1',
            ]
        );

        $response = $this->request->send();

        $this->assertInstanceOf(
            'Omnipay\NABTransact\Message\DirectPostCompletePurchaseResponse',
            $response
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('271337', $response->getTransactionReference());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('00', $response->getCode());
    }

    public function testFailure()
    {
        $this->request->initialize([
            'amount'              => '465.18',
            'transactionPassword' => 'abcd1234',
        ]);

        $this->getHttpRequest()->query->replace([
            'timestamp'            => '20161126051715',
            'callback_status_code' => '404',
            'fingerprint'          => 'd94853f8c8d1bc1b21978f64cc866d824d2feca61224afb9a4e31a9c286d0008',
            'txnid'                => '274279',
            'merchant'             => 'XYZ0010',
            'restext'              => 'Customer Dispute',
            'rescode'              => '18',
            'expirydate'           => '102030',
            'settdate'             => '20161126',
            'refid'                => 'ORDER-ZYX8',
            'pan'                  => '444433...111',
            'summarycode'          => '2',
        ]);

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\DirectPostCompletePurchaseResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('274279', $response->getTransactionReference());
        $this->assertSame('Customer Dispute', $response->getMessage());
        $this->assertSame('18', $response->getCode());
    }
}
