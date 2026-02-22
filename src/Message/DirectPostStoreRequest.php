<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Store Only Request.
 */
class DirectPostStoreRequest extends DirectPostAuthorizeRequest
{
    /**
     * @var string
     */
    public $txnType = '8';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('returnUrl', 'card');

        if (!$this->getAmount()) {
            $this->setAmount('0.00');
        }

        return parent::getData();
    }
}
