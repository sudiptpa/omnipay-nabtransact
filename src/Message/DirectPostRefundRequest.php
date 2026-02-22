<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Enums\TransactionType;

/**
 * DirectPost refund request.
 */
class DirectPostRefundRequest extends DirectPostOperationRequest
{
    /**
     * @var string
     */
    public $txnType = TransactionType::REFUND;
}
