<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;

class DirectPostStoreRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new DirectPostStoreRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'          => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'returnUrl'           => 'https://www.example.com/return',
            'transactionId'       => 'STORE-ORDER-100',
            'card'                => [
                'number'      => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear'  => '2030',
                'cvv'         => '123',
            ],
        ]);
    }

    public function testStoreDefaultsAmountAndUsesStoreTxnType()
    {
        $data = $this->request->getData();

        $this->assertSame('8', $data['EPS_TXNTYPE']);
        $this->assertSame('0.00', $data['EPS_AMOUNT']);
        $this->assertSame('true', $data['EPS_STORE']);
        $this->assertSame('TOKEN', $data['EPS_STORETYPE']);
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
    }

    public function testStoreFingerprintIncludesStoreType()
    {
        $this->request->setStoreType('TOKEN');

        $data = $this->request->getData();
        $data['EPS_TIMESTAMP'] = '20190215173250';

        $this->assertSame(
            '1eaf7fc13922cd2222de4866cb41ded4c4c7358cb2a2728759aaf4efb3dd015e',
            $this->request->generateFingerprint($data)
        );
    }
}
