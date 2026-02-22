<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class SecureXMLRiskPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureXMLRiskPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'testMode' => true,
            'amount' => '12.00',
            'transactionId' => '1234',
            'ip' => '1.1.1.1',
            'card' => [
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'cvv' => '123',
                'email' => 'example@example.com',
                'billingPostcode' => '12345',
                'billingCity' => 'Billstown',
                'billingCountry' => 'US',
            ],
        ]);
    }

    public function testBuildsBuyerInfoWithLastNameField()
    {
        $xml = (string) $this->request->getData()->asXML();

        $this->assertStringContainsString('<firstName>Example</firstName>', $xml);
        $this->assertStringContainsString('<lastName>User</lastName>', $xml);
        $this->assertStringNotContainsString('<firstName>User</firstName>', $xml);
        $this->assertStringContainsString('<ip>1.1.1.1</ip>', $xml);
    }
}
