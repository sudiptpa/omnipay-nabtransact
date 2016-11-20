<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class SecureXMLPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureXMLPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            array(
                'merchantId' => 'XYZ0010',
                'transactionPassword' => 'abcd1234',
                'testMode' => true,
                'amount' => '12.00',
                'transactionId' => '1234',
                'card' => array(
                    'number' => '4444333322221111',
                    'expiryMonth' => '10',
                    'expiryYear' => '2030',
                    'cvv' => '123',
                    'cardHolderName' => 'Sujip Thapa',
                ),
            )
        );
    }

    public function testSendSuccess()
    {
        $data = array();

        $data['RequestType'] = "Payment";
        $data['statusDescription'] = "Normal";
        $data['statusCode'] = "000";
        $data['apiVersion'] = "xml-4.2";
        $data['txnType'] = "10";
        $data['txnSource'] = "23";
        $data['amount'] = "12.00";
        $data['currency'] = "AUD";
        $data['approved'] = "Yes";
        $data['responseCode'] = "00";
        $data['responseText'] = "Approved";
        $data['cardDescription'] = "Visa";
        $data['expiryDate'] = "10/30";
        $data['cardType'] = "6";

        $response = new SecureXMLResponse($this->getMockRequest(), $data);

        $response = $this->request->send();
        $data = $response->getData();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('0', (string) $data->Payment->TxnList->Txn->txnType);
        $this->assertSame('1234', $response->getTransactionReference());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('Approved', $response->getMessage());
    }
}
