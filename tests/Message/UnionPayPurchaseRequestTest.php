<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\NABTransact\Tests\Support\TestCase;

class UnionPayPurchaseRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new UnionPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'          => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount'              => '12.00',
            'returnUrl'           => 'https://www.example.com/return',
            'transactionId'       => 'GHJGG76756556',
        ]);
    }

    public function testFingerprint()
    {
        $data = $this->request->getData();
        $data['EPS_TIMESTAMP'] = '20190215173250';

        $this->assertSame('1b72d460b36e6633bf57b119d6bd3635da6fc57324a622c1d41b5b26f08fce8d', $this->request->generateFingerprint($data));
    }

    public function testPurchase()
    {
        $response = $this->request->send();

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\UnionPayPurchaseResponse::class, $response);

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

    public function testAllowsSupportedCurrencyForUpop()
    {
        $this->request->setCurrency('AUD');

        $data = $this->request->getData();

        $this->assertSame('UPOP', $data['EPS_PAYMENTCHOICE']);
        $this->assertSame('AUD', $data['EPS_CURRENCY']);
    }

    public function testRejectsUnsupportedCurrencyForUpop()
    {
        $this->request->setCurrency('USD');

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('UPOP only supports AUD or CNY currencies.');

        $this->request->getData();
    }

    public function testRejectsRiskManagementForUpop()
    {
        $this->request->setHasRiskManagementEnabled(true);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('UPOP does not support risk-management transaction types.');

        $this->request->getData();
    }

    public function testRejectsEmvForUpop()
    {
        $this->request->setHasEMV3DSEnabled(true);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('UPOP does not support EMV 3DS transaction types.');

        $this->request->getData();
    }
}
