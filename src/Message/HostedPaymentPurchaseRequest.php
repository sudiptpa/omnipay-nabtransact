<?php

namespace Omnipay\NABTransact\Message;

/**
 * HostedPayment Purchase Request.
 */
class HostedPaymentPurchaseRequest extends AbstractRequest
{
    public $liveEndpoint = 'https://transact.nab.com.au/test/hpp/payment';

    public $testEndpoint = 'https://transact.nab.com.au/live/hpp/payment';

    public function getData()
    {
        $this->validate(
            'amount',
            'returnUrl',
            'transactionId',
            'merchantId',
            'paymentAlertEmail'
        );

        $data = [];

        $data['vendor_name'] = $this->getMerchantId();
        $data['payment_alert'] = $this->getPaymentAlertEmail();
        $data['payment_reference'] = $this->getTransactionId();
        $data['currency'] = $this->getCurrency();
        $data['return_link_url'] = $this->getReturnUrl();
        $data['reply_link_url'] = $this->getNotifyUrl() ?: $this->getReturnUrl();
        $data['return_link_text'] = $this->getReturnUrlText();
        $data['total_amount'] = $this->getAmount();

        return $data;
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function getPaymentAlertEmail()
    {
        return $this->getParameter('paymentAlertEmail');
    }

    public function getReturnUrlText()
    {
        return $this->getParameter('returnUrlText');
    }

    public function sendData($data)
    {
        return $this->response = new HostedPaymentPurchaseResponse($this, $data, $this->getEndpoint());
    }

    public function setPaymentAlertEmail($value)
    {
        return $this->setParameter('paymentAlertEmail', $value);
    }

    public function setReturnUrlText($value)
    {
        return $this->setParameter('returnUrlText', $value);
    }
}
