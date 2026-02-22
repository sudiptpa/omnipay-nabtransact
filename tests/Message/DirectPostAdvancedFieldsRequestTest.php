<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;

class DirectPostAdvancedFieldsRequestTest extends TestCase
{
    public function testAddsAdvancedDirectPostFieldsToPayload()
    {
        $request = new DirectPostPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount' => '112.00',
            'currency' => 'AUD',
            'returnUrl' => 'https://www.example.com/return',
            'notifyUrl' => 'https://www.example.com/callback',
            'transactionId' => 'ORDER-ADV-100',
            'card' => [
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'cvv' => '123',
            ],
            'surchargeEnabled' => true,
            'surchargeAmount' => '12.00',
            'surchargeRate' => '5.00',
            'surchargeFee' => '7.00',
            'cardScheme' => 'scheme',
            'resultParams' => 'merchant,refid,rescode,restext',
            'callbackParams' => 'merchant,refid,rescode,restext',
        ]);

        $data = $request->getData();

        $this->assertSame('scheme', $data['EPS_CARDSCHEME']);
        $this->assertSame('true', $data['EPS_SURCHARGEENABLED']);
        $this->assertSame('12.00', $data['EPS_SURCHARGEAMOUNT']);
        $this->assertSame('5.00', $data['EPS_SURCHARGERATE']);
        $this->assertSame('7.00', $data['EPS_SURCHARGEFEE']);
        $this->assertSame('merchant,refid,rescode,restext', $data['EPS_RESULTPARAMS']);
        $this->assertSame('merchant,refid,rescode,restext', $data['EPS_CALLBACKPARAMS']);
    }
}

