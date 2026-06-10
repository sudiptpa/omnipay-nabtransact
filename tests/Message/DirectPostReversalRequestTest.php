<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Tests\Support\TestCase;
use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;

class DirectPostReversalRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new DirectPostReversalRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'merchantId'           => 'XYZ0010',
            'transactionPassword'  => 'abcd1234',
            'amount'               => '12.00',
            'transactionId'        => 'VOID-ORDER-100',
            'transactionReference' => 'NAB-TXN-VOID-100',
        ]);
    }

    public function testGetDataBuildsReversalPayload()
    {
        $data = $this->request->getData();

        $this->assertSame('REVERSAL', $data['EPS_TXNTYPE']);
        $this->assertSame('NAB-TXN-VOID-100', $data['EPS_ORIGINALTXNID']);
        $this->assertArrayHasKey('EPS_FINGERPRINT', $data);
    }

    public function testSendParsesXmlResponse()
    {
        $request = $this->request;

        $request->setTransport(new class () implements TransportInterface {
            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                return new TransportResponse(200, '<response><rescode>00</rescode><restext>Reversed</restext><txnid>VOID-1</txnid></response>');
            }
        });

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Reversed', $response->getMessage());
        $this->assertSame('VOID-1', $response->getTransactionReference());
    }
}
