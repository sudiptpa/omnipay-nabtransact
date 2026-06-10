<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;
use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;

class DirectPostRefundRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new DirectPostRefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'           => 'XYZ0010',
            'transactionPassword'  => 'abcd1234',
            'amount'               => '5.00',
            'transactionId'        => 'REFUND-ORDER-100',
            'transactionReference' => 'NAB-TXN-100',
        ]);
    }

    public function testGetDataBuildsRefundPayload()
    {
        $data = $this->request->getData();

        $this->assertSame('REFUND', $data['EPS_TXNTYPE']);
        $this->assertSame('NAB-TXN-100', $data['EPS_ORIGINALTXNID']);
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
    }

    public function testSendParsesJsonResponse()
    {
        $request = $this->request;

        $request->setTransport(new class () implements TransportInterface {
            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                return new TransportResponse(200, '{"rescode":"00","restext":"Refunded","txnid":"REF-1"}');
            }
        });

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Refunded', $response->getMessage());
        $this->assertSame('REF-1', $response->getTransactionReference());
    }
}
