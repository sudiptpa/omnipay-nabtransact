<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Enums\TransactionType;

/**
 * DirectPost reversal (void) request.
 */
class DirectPostReversalRequest extends DirectPostOperationRequest
{
    /**
     * @var string
     */
    public $txnType = TransactionType::REVERSAL;
}
