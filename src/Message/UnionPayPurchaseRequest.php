<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * UnionPayPurchaseRequest.
 */
class UnionPayPurchaseRequest extends DirectPostAbstractRequest
{
    /**
     * @var string
     */
    public $txnType = '0';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'transactionId');

        if ((bool) $this->getHasRiskManagementEnabled()) {
            throw new InvalidRequestException('UPOP does not support risk-management transaction types.');
        }

        if ((bool) $this->getHasEMV3DSEnabled()) {
            throw new InvalidRequestException('UPOP does not support EMV 3DS transaction types.');
        }

        if ($this->getCurrency() !== null) {
            $currency = strtoupper((string) $this->getCurrency());
            if (!in_array($currency, ['AUD', 'CNY'], true)) {
                throw new InvalidRequestException('UPOP only supports AUD or CNY currencies.');
            }
        }

        $data = $this->getBaseData();

        $data['EPS_PAYMENTCHOICE'] = 'UPOP';

        return $data;
    }

    /**
     * @param $data
     *
     * @return \Omnipay\NABTransact\Message\UnionPayPurchaseResponse
     */
    public function sendData($data)
    {
        $redirectUrl = $this->getEndpoint().'?'.http_build_query($data);

        return $this->response = new UnionPayPurchaseResponse($this, $data, $redirectUrl);
    }
}
