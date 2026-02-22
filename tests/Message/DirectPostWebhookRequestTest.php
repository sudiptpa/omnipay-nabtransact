<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Tests\TestCase;

class DirectPostWebhookRequestTest extends TestCase
{
    public function testVerifyFingerprintAndTypoAliasAreBackwardCompatible()
    {
        $payload = [
            'merchant' => 'XYZ0010',
            'txn_password' => 'abcd1234',
            'refid' => 'ORDER-123',
            'amount' => '10.00',
            'timestamp' => '20260222000000',
            'summarycode' => '1',
            'restext' => 'Approved',
            'rescode' => '00',
            'txnid' => '10001',
        ];

        $request = new DirectPostWebhookRequest($payload);
        $fingerprint = $request->generateResponseFingerprint($payload);

        $responseFromCorrectMethod = $request->verifyFingerPrint($fingerprint);
        $responseFromTypoAlias = $request->vefiyFingerPrint($fingerprint);

        $this->assertTrue($responseFromCorrectMethod->isSuccessful());
        $this->assertTrue($responseFromTypoAlias->isSuccessful());
    }

    public function testInvalidFingerprintMarksFailure()
    {
        $payload = [
            'merchant' => 'XYZ0010',
            'txn_password' => 'abcd1234',
            'refid' => 'ORDER-123',
            'amount' => '10.00',
            'timestamp' => '20260222000000',
            'summarycode' => '1',
            'restext' => 'Approved',
            'rescode' => '00',
            'txnid' => '10001',
        ];

        $request = new DirectPostWebhookRequest($payload);
        $response = $request->sendData(['fingerprint' => 'invalid']);

        $this->assertFalse($response->isSuccessful());
        $this->assertStringContainsString('Invalid fingerprint', (string) $response->getMessage());
    }
}
