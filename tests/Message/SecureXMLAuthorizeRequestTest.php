<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class SecureXMLAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureXMLAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            [
                'merchantId'          => 'XYZ0010',
                'transactionPassword' => 'abcd1234',
                'testMode'            => true,
                'amount'              => '12.00',
                'transactionId'       => '1234',
                'card'                => [
                    'number'         => '4444333322221111',
                    'expiryMonth'    => '10',
                    'expiryYear'     => '2030',
                    'cvv'            => '123',
                    'cardHolderName' => 'Sujip Thapa',
                ],
            ]
        );
    }

    public function testSendSuccess()
    {
        $data = [];

        $data['RequestType'] = 'Payment';
        $data['statusDescription'] = 'Normal';
        $data['statusCode'] = '000';
        $data['apiVersion'] = 'xml-4.2';
        $data['txnType'] = '10';
        $data['txnSource'] = '23';
        $data['amount'] = '12.00';
        $data['currency'] = 'AUD';
        $data['approved'] = 'Yes';
        $data['responseCode'] = '00';
        $data['responseText'] = 'Approved';
        $data['txnID'] = '1234';
        $data['cardDescription'] = 'Visa';
        $data['expiryDate'] = '10/30';
        $data['cardType'] = '6';

        $response = new SecureXMLResponse($this->getMockRequest(), $data);

        $response = $this->request->send();

        $data = $response->getData();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('23', $response->getTransactionSource());
        $this->assertSame('AUD', $response->getTransactionCurrency());
        $this->assertSame('1200', $response->getTransactionAmount());
        $this->assertNotNull($response->getSettlementDate());
        $this->assertNotNull($response->getTransactionId());
        $this->assertSame('Approved', $response->getMessage());
        $this->assertSame('00', $response->getCode());
        $this->assertSame('10', (string) $data->Payment->TxnList->Txn->txnType);
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('SecureXMLAuthorizeRequestFail.txt');
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('510', $response->getCode());
        $this->assertSame('Unable To Connect To Server', $response->getMessage());
        $this->assertSame('20161122083125000+345', $response->getMessageTimestamp());
    }

    public function testInsufficientFundsFailure()
    {
        $this->setMockHttpResponse('SecureXMLAuthorizeRequestInsufficientFundsFail.txt');
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('51', $response->getCode());
        $this->assertSame('Insufficient Funds', $response->getMessage());
    }

    public function testInvalidMerchantFailure()
    {
        $this->setMockHttpResponse('SecureXMLAuthorizeRequestInvalidMerchantFail.txt');
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('504', $response->getCode());
        $this->assertSame('Invalid merchant ABC0030', $response->getMessage());
    }

    public function testInvalidMerchantIDFailure()
    {
        $this->setMockHttpResponse('SecureXMLAuthorizeRequestInvalidMerchantIDFail.txt');
        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('504', $response->getCode());
        $this->assertSame('Invalid merchant ID', $response->getMessage());
    }

    public function testSetMessageId()
    {
        $this->request->setMessageId('8af793f9af34bea0cf40f5fc011e0c');
        $this->assertSame('8af793f9af34bea0cf40f5fc011e0c', $this->request->getMessageId());
    }

    public function testAutogeneratedMessageId()
    {
        $this->assertNotNull($this->request->getMessageId());
        $this->assertSame(30, strlen($this->request->getMessageId()));
    }
}
