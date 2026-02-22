<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Transport\TransportInterface;
use Omnipay\NABTransact\Transport\TransportResponse;
use Omnipay\NABTransact\Tests\Support\TestCase;

class SecureXMLTransportRequestTest extends TestCase
{
    public function testUsesCustomTransportWhenProvided()
    {
        $request = new SecureXMLPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize([
            'merchantId' => 'XYZ0010',
            'transactionPassword' => 'abcd1234',
            'testMode' => true,
            'amount' => '12.00',
            'transactionId' => '1234',
            'card' => [
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'cvv' => '123',
                'cardHolderName' => 'Sujip Thapa',
            ],
        ]);

        $capture = (object) ['called' => false, 'method' => null, 'url' => null];

        $transport = new class($capture) implements TransportInterface {
            private $capture;

            public function __construct($capture)
            {
                $this->capture = $capture;
            }

            public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60)
            {
                $this->capture->called = true;
                $this->capture->method = $method;
                $this->capture->url = $url;

                return new TransportResponse(200, <<<XML
<NABTransactMessage>
  <MessageInfo><messageTimestamp>20260222000000000+0000</messageTimestamp></MessageInfo>
  <Status><statusCode>000</statusCode><statusDescription>Normal</statusDescription></Status>
  <RequestType>Payment</RequestType>
  <Payment>
    <TxnList count="1">
      <Txn ID="1">
        <approved>Yes</approved>
        <responseCode>00</responseCode>
        <responseText>Approved</responseText>
        <purchaseOrderNo>1234</purchaseOrderNo>
        <txnID>1000</txnID>
        <amount>1200</amount>
        <currency>AUD</currency>
        <txnSource>23</txnSource>
      </Txn>
    </TxnList>
  </Payment>
</NABTransactMessage>
XML
                );
            }
        };

        $request->setTransport($transport);

        $response = $request->send();

        $this->assertTrue($capture->called);
        $this->assertSame('POST', $capture->method);
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
    }
}
