<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;
use Omnipay\Tests\TestCase;

class DirectPostCaptureRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new DirectPostCaptureRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'amount' => '12.00',
            'currency' => 'AUD',
            'transactionId' => 'CAPTURE-ORDER-100',
            'transactionReference' => 'NAB-ORIG-100',
        ]);
    }

    public function testGetDataBuildsCapturePayload()
    {
        $data = $this->request->getData();

        $this->assertSame('COMPLETE', $data['EPS_TXNTYPE']);
        $this->assertSame('NAB-ORIG-100', $data['EPS_TXNID']);
        $this->assertSame('12.00', $data['EPS_AMOUNT']);
        $this->assertSame('AUD', $data['EPS_CURRENCY']);
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
    }

    public function testSendParsesQueryStringResponse()
    {
        $request = $this->request;

        $request->setTransport(new class implements TransportInterface {
            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                return new TransportResponse(200, 'rescode=00&restext=Approved&txnid=CAPTURED-1');
            }
        });

        $response = $request->send();

        $this->assertInstanceOf(\Omnipay\NABTransact\Message\DirectPostApiResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('CAPTURED-1', $response->getTransactionReference());
    }
}
