<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Purchase Request.
 */
class DirectPostPurchaseRequest extends DirectPostAuthorizeRequest
{
    public $txnType = '0';
}
