<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;

class DirectPostApiResponseTest extends TestCase
{
    public function testSuccessfulByResponseCode()
    {
        $request = new DirectPostRefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $response = new DirectPostApiResponse($request, [
            'rescode'          => '00',
            'restext'          => 'Approved',
            'txnid'            => 'TXN-100',
            'http_status_code' => 500,
            'raw'              => 'rescode=00',
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('TXN-100', $response->getTransactionReference());
        $this->assertSame(500, $response->getHttpStatusCode());
        $this->assertSame('rescode=00', $response->getRawResponse());
    }

    public function testSuccessfulByHttpStatusWhenNoResultCode()
    {
        $request = new DirectPostRefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $response = new DirectPostApiResponse($request, [
            'http_status_code' => 200,
            'message'          => 'OK',
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getCode());
        $this->assertSame('OK', $response->getMessage());
    }

    public function testFailureWhenNoSignals()
    {
        $request = new DirectPostRefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $response = new DirectPostApiResponse($request, []);

        $this->assertFalse($response->isSuccessful());
    }
}
