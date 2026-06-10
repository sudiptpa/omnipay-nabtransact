<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Enums\TransactionType;
use Omnipay\NABTransact\Tests\Support\TestCase;

/**
 * Verifies the Direct Post fingerprint is composed using the field order
 * defined in the NAB Direct Post v2 integration guide (sections 2.3.6.1 -
 * 2.3.6.4).
 */
class DirectPostFingerprintRequestTest extends TestCase
{
    private const PASSWORD = 'abcd1234';

    /**
     * @param array<string,mixed> $overrides
     *
     * @return DirectPostPurchaseRequest
     */
    private function buildRequest(array $overrides = [])
    {
        $request = new DirectPostPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize(array_merge([
            'merchantId'          => 'XYZ0010',
            'transactionPassword' => self::PASSWORD,
            'amount'              => '100.00',
            'currency'            => 'AUD',
            'returnUrl'           => 'https://www.example.com/return',
            'transactionId'       => 'ORDER-100',
            'card'                => [
                'number'      => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear'  => '2030',
                'cvv'         => '123',
            ],
        ], $overrides));

        return $request;
    }

    /**
     * Rebuilds the expected fingerprint from the resulting payload using the
     * documented field order, then hashes it the same way the gateway does.
     *
     * @param array<string,mixed> $data
     * @param array<int,string>   $orderedFields
     *
     * @return string
     */
    private function expectedFingerprint(array $data, array $orderedFields)
    {
        $parts = [
            $data['EPS_MERCHANT'],
            self::PASSWORD,
            $data['EPS_TXNTYPE'],
            $data['EPS_REFERENCEID'],
            $data['EPS_AMOUNT'],
            $data['EPS_TIMESTAMP'],
        ];

        foreach ($orderedFields as $field) {
            $parts[] = $data[$field];
        }

        return hash_hmac('sha256', implode('|', $parts), self::PASSWORD);
    }

    public function testStandardFingerprint()
    {
        $data = $this->buildRequest()->getData();

        $this->assertSame(
            $this->expectedFingerprint($data, []),
            $data['EPS_FINGERPRINT']
        );
    }

    public function testSurchargeFingerprintUsesFeeRateAmountOrderAndExcludesEnabledFlag()
    {
        $data = $this->buildRequest([
            'surchargeEnabled' => true,
            'surchargeFee'     => '7.00',
            'surchargeRate'    => '5.00',
            'surchargeAmount'  => '12.00',
        ])->getData();

        $this->assertSame('true', $data['EPS_SURCHARGEENABLED']);

        $this->assertSame(
            $this->expectedFingerprint($data, [
                'EPS_SURCHARGEFEE',
                'EPS_SURCHARGERATE',
                'EPS_SURCHARGEAMOUNT',
            ]),
            $data['EPS_FINGERPRINT']
        );
    }

    public function testEmv3dsFingerprintAppendsOrderId()
    {
        $request = $this->buildRequest(['transactionReference' => 'EMV-ORDER-ID-123']);
        $request->setHasEMV3DSEnabled(true);

        $data = $request->getData();

        $this->assertSame('EMV-ORDER-ID-123', $data['EPS_ORDERID']);
        $this->assertSame((string) TransactionType::PAYMENT_3DS_EMV3DS, $data['EPS_TXNTYPE']);

        $this->assertSame(
            $this->expectedFingerprint($data, ['EPS_ORDERID']),
            $data['EPS_FINGERPRINT']
        );
    }

    public function testEmv3dsWithSurchargeOrdersOrderIdBeforeSurcharge()
    {
        $request = $this->buildRequest([
            'transactionReference' => 'EMV-ORDER-ID-123',
            'surchargeEnabled'     => true,
            'surchargeFee'         => '7.00',
            'surchargeRate'        => '5.00',
            'surchargeAmount'      => '12.00',
        ]);
        $request->setHasEMV3DSEnabled(true);

        $data = $request->getData();

        $this->assertSame(
            $this->expectedFingerprint($data, [
                'EPS_ORDERID',
                'EPS_SURCHARGEFEE',
                'EPS_SURCHARGERATE',
                'EPS_SURCHARGEAMOUNT',
            ]),
            $data['EPS_FINGERPRINT']
        );
    }

    public function testMcrFingerprintAppendsCardScheme()
    {
        $data = $this->buildRequest(['cardScheme' => 'scheme'])->getData();

        $this->assertSame(
            $this->expectedFingerprint($data, ['EPS_CARDSCHEME']),
            $data['EPS_FINGERPRINT']
        );
    }

    public function testMcrWithSurchargeOrdersCardSchemeThenSurcharge()
    {
        $data = $this->buildRequest([
            'cardScheme'       => 'scheme',
            'surchargeEnabled' => true,
            'surchargeFee'     => '7.00',
            'surchargeRate'    => '5.00',
            'surchargeAmount'  => '12.00',
        ])->getData();

        $this->assertSame(
            $this->expectedFingerprint($data, [
                'EPS_CARDSCHEME',
                'EPS_SURCHARGEFEE',
                'EPS_SURCHARGERATE',
                'EPS_SURCHARGEAMOUNT',
            ]),
            $data['EPS_FINGERPRINT']
        );
    }

    public function testMcrWithSurchargeAndEmv3dsOrdersOrderIdLast()
    {
        $request = $this->buildRequest([
            'transactionReference' => 'EMV-ORDER-ID-123',
            'cardScheme'           => 'eftpos',
            'surchargeEnabled'     => true,
            'surchargeFee'         => '7.00',
            'surchargeRate'        => '5.00',
            'surchargeAmount'      => '12.00',
        ]);
        $request->setHasEMV3DSEnabled(true);

        $data = $request->getData();

        $this->assertSame(
            $this->expectedFingerprint($data, [
                'EPS_CARDSCHEME',
                'EPS_SURCHARGEFEE',
                'EPS_SURCHARGERATE',
                'EPS_SURCHARGEAMOUNT',
                'EPS_ORDERID',
            ]),
            $data['EPS_FINGERPRINT']
        );
    }
}
