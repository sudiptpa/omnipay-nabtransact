<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\CreditCard;
use Omnipay\NABTransact\Enums\TransactionType;

/**
 * NABTransact Direct Post Abstract Request.
 */
abstract class DirectPostAbstractRequest extends AbstractRequest
{
    /**
     * @var string
     */
    public $testEndpoint = 'https://demo.transact.nab.com.au/directpostv2/authorise';

    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/live/directpostv2/authorise';

    /**
     * @var string|int
     */
    protected $txnType = '0';

    public function getResultParams()
    {
        return $this->getParameter('resultParams');
    }

    public function setResultParams($value)
    {
        return $this->setParameter('resultParams', $value);
    }

    public function getCallbackParams()
    {
        return $this->getParameter('callbackParams');
    }

    public function setCallbackParams($value)
    {
        return $this->setParameter('callbackParams', $value);
    }

    public function getCardScheme()
    {
        return $this->getParameter('cardScheme');
    }

    public function setCardScheme($value)
    {
        return $this->setParameter('cardScheme', $value);
    }

    public function getSurchargeEnabled()
    {
        return $this->getParameter('surchargeEnabled');
    }

    public function setSurchargeEnabled($value)
    {
        return $this->setParameter('surchargeEnabled', $value);
    }

    public function getSurchargeAmount()
    {
        return $this->getParameter('surchargeAmount');
    }

    public function setSurchargeAmount($value)
    {
        return $this->setParameter('surchargeAmount', $value);
    }

    public function getSurchargeRate()
    {
        return $this->getParameter('surchargeRate');
    }

    public function setSurchargeRate($value)
    {
        return $this->setParameter('surchargeRate', $value);
    }

    public function getSurchargeFee()
    {
        return $this->getParameter('surchargeFee');
    }

    public function setSurchargeFee($value)
    {
        return $this->setParameter('surchargeFee', $value);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return string
     */
    protected function buildFingerprintFromFields(array $data)
    {
        $fields = [
            'EPS_MERCHANT',
            '__TRANSACTION_PASSWORD__',
            'EPS_TXNTYPE',
            'EPS_REFERENCEID',
            'EPS_AMOUNT',
            'EPS_TIMESTAMP',
        ];

        if (isset($data['EPS_ORDERID'])) {
            $fields[] = 'EPS_ORDERID';
        }

        if (isset($data['EPS_CARDSCHEME'])) {
            $fields[] = 'EPS_CARDSCHEME';
        }

        if (isset($data['EPS_SURCHARGEENABLED'])) {
            $fields[] = 'EPS_SURCHARGEENABLED';
            $fields[] = 'EPS_SURCHARGEAMOUNT';
            $fields[] = 'EPS_SURCHARGERATE';
            $fields[] = 'EPS_SURCHARGEFEE';
        }

        $hashable = [];
        foreach ($fields as $field) {
            if ($field === '__TRANSACTION_PASSWORD__') {
                $hashable[] = (string) $this->getTransactionPassword();

                continue;
            }

            $hashable[] = isset($data[$field]) ? (string) $data[$field] : '';
        }

        return hash_hmac('sha256', implode('|', $hashable), $this->getTransactionPassword());
    }

    /**
     * @return string
     */
    protected function resolveTxnType()
    {
        $isPreauth = (string) $this->txnType === (string) TransactionType::NORMAL_PREAUTH;
        $risk = (bool) $this->getHasRiskManagementEnabled();
        $emv = (bool) $this->getHasEMV3DSEnabled();

        if ($risk && $emv) {
            return (string) ($isPreauth ? TransactionType::PREAUTH_RISK_MANAGEMENT_3DS_EMV3DS : TransactionType::PAYMENT_RISK_MANAGEMENT_3DS_EMV3DS);
        }

        if ($risk) {
            return (string) ($isPreauth ? TransactionType::PREAUTH_RISK_MANAGEMENT : TransactionType::PAYMENT_RISK_MANAGEMENT);
        }

        if ($emv) {
            return (string) ($isPreauth ? TransactionType::PREAUTH_3DS_EMV3DS : TransactionType::PAYMENT_3DS_EMV3DS);
        }

        return (string) $this->txnType;
    }

    /**
     * @param array $data
     */
    public function generateFingerprint(array $data)
    {
        return $this->buildFingerprintFromFields($data);
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        $data = [];

        $data['EPS_MERCHANT'] = $this->getMerchantId();
        $data['EPS_TXNTYPE'] = $this->resolveTxnType();
        $data['EPS_REFERENCEID'] = $this->getTransactionId();
        $data['EPS_AMOUNT'] = $this->getAmount();
        $data['EPS_TIMESTAMP'] = gmdate('YmdHis');
        $data['EPS_RESULTURL'] = $this->getReturnUrl();
        $data['EPS_IP'] = $this->getClientIp();
        $data['EPS_REDIRECT'] = 'TRUE';

        if ($this->getNotifyUrl()) {
            $data['EPS_CALLBACKURL'] = $this->getNotifyUrl();
        }

        if ($resultParams = $this->getResultParams()) {
            $data['EPS_RESULTPARAMS'] = $resultParams;
        }

        if ($callbackParams = $this->getCallbackParams()) {
            $data['EPS_CALLBACKPARAMS'] = $callbackParams;
        }

        if ($currency = $this->getCurrency()) {
            $data['EPS_CURRENCY'] = $currency;
        }

        if ($cardScheme = $this->getCardScheme()) {
            $data['EPS_CARDSCHEME'] = $cardScheme;
        }

        $card = $this->getParameter('card');

        if ($card instanceof CreditCard) {
            if ($billingFirstName = $card->getBillingFirstName()) {
                $data['EPS_FIRSTNAME'] = $billingFirstName;
            }

            if ($billingLastName = $card->getBillingLastName()) {
                $data['EPS_LASTNAME'] = $billingLastName;
            }

            if ($billingPostcode = $card->getBillingPostcode()) {
                $data['EPS_ZIPCODE'] = $billingPostcode;
            }

            if ($billingCity = $card->getBillingCity()) {
                $data['EPS_TOWN'] = $billingCity;
            }

            if ($billingCountry = $card->getBillingCountry()) {
                $data['EPS_BILLINGCOUNTRY'] = $billingCountry;
            }

            if ($shippingCountry = $card->getShippingCountry()) {
                $data['EPS_DELIVERYCOUNTRY'] = $shippingCountry;
            }

            if ($emailAddress = $card->getEmail()) {
                $data['EPS_EMAILADDRESS'] = $emailAddress;
            }
        }

        if ($this->getHasEMV3DSEnabled()) {
            $data['EPS_ORDERID'] = $this->getTransactionReference();
        }

        if ((bool) $this->getSurchargeEnabled()) {
            $data['EPS_SURCHARGEENABLED'] = 'true';
            $data['EPS_SURCHARGEAMOUNT'] = (string) ($this->getSurchargeAmount() !== null ? $this->getSurchargeAmount() : '0.00');
            $data['EPS_SURCHARGERATE'] = (string) ($this->getSurchargeRate() !== null ? $this->getSurchargeRate() : '0.00');
            $data['EPS_SURCHARGEFEE'] = (string) ($this->getSurchargeFee() !== null ? $this->getSurchargeFee() : '0.00');
        }

        $data['EPS_FINGERPRINT'] = $this->generateFingerprint($data);

        return $data;
    }
}
