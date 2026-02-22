<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostTransactionTypeRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->purchaseRequest = new DirectPostPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->purchaseRequest->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount' => '12.00',
            'returnUrl' => 'https://www.example.com/return',
            'transactionId' => 'ORDER-100',
            'transactionReference' => 'ORDER-REF-100',
            'card' => [
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'cvv' => '123',
            ],
        ]);

        $this->authorizeRequest = new DirectPostAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->authorizeRequest->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount' => '12.00',
            'returnUrl' => 'https://www.example.com/return',
            'transactionId' => 'ORDER-101',
            'transactionReference' => 'ORDER-REF-101',
            'card' => [
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'cvv' => '123',
            ],
        ]);
    }

    public function testRiskManagementPurchaseTransactionType()
    {
        $this->purchaseRequest->setHasRiskManagementEnabled(true);

        $data = $this->purchaseRequest->getData();

        $this->assertSame('2', $data['EPS_TXNTYPE']);
    }

    public function testRiskManagementAuthorizeTransactionType()
    {
        $this->authorizeRequest->setHasRiskManagementEnabled(true);

        $data = $this->authorizeRequest->getData();

        $this->assertSame('3', $data['EPS_TXNTYPE']);
    }

    public function testRiskManagementWithEmv3dsPurchaseTransactionType()
    {
        $this->purchaseRequest->setHasRiskManagementEnabled(true);
        $this->purchaseRequest->setHasEMV3DSEnabled(true);

        $data = $this->purchaseRequest->getData();

        $this->assertSame('6', $data['EPS_TXNTYPE']);
        $this->assertArrayHasKey('EPS_ORDERID', $data);
    }

    public function testRiskManagementWithEmv3dsAuthorizeTransactionType()
    {
        $this->authorizeRequest->setHasRiskManagementEnabled(true);
        $this->authorizeRequest->setHasEMV3DSEnabled(true);

        $data = $this->authorizeRequest->getData();

        $this->assertSame('7', $data['EPS_TXNTYPE']);
        $this->assertArrayHasKey('EPS_ORDERID', $data);
    }
}
