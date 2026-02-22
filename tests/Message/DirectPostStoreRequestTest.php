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
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
    }
}
