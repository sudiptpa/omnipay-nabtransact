<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * NABTransact HostedPaymentCompletePurchaseResponse.
 */
class HostedPaymentCompletePurchaseResponse extends AbstractResponse
{
    /**
     * @param RequestInterface $request
     * @param                  $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        if (!is_array($data)) {
            parse_str($data, $data);
        }

        parent::__construct($request, $data);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->summaryCode() && in_array($this->getCode(), ['00', '08', '11'], true);
    }

    /**
     * @return bool
     */
    public function summaryCode()
    {
        return isset($this->data['summarycode']) && (int) $this->data['summarycode'] === 1;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->data['restext']) ? $this->data['restext'] : null;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return isset($this->data['rescode']) ? $this->data['rescode'] : null;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        return isset($this->data['txnid']) ? $this->data['txnid'] : null;
    }
}
