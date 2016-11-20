<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Message\SecureXMLEchoTestRequest;
use Omnipay\NABTransact\Message\SecureXMLResponse;
use Omnipay\Tests\TestCase;

class SecureXMLEchoTestRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureXMLEchoTestRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            array(
                'merchantId' => 'XYZ0010',
                'transactionPassword' => 'abcd1234',
                'testMode' => true,
            )
        );
    }

    public function testSuccess()
    {
        $data = array();

        $data['RequestType'] = "Echo";
        $data['statusDescription'] = "Normal";
        $data['statusCode'] = "000";
        $data['apiVersion'] = "xml-4.2";

        $response = new SecureXMLResponse($this->getMockRequest(), $data);

        $response = $this->request->send();
        $data = $response->getData();

        $this->assertInstanceOf('Omnipay\NABTransact\Message\SecureXMLResponse', $response);
        $this->assertSame('Normal', $response->getMessage());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('000', $response->getStatusCode());
    }
}
