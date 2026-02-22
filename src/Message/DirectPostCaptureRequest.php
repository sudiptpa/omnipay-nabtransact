<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Enums\TransactionType;

/**
 * DirectPost complete-preauth (capture) request.
 */
class DirectPostCaptureRequest extends DirectPostOperationRequest
{
    /**
     * @var string
     */
    public $txnType = TransactionType::COMPLETE_PREAUTH;

    /**
     * @var string
     */
    protected $targetTransactionField = 'EPS_TXNID';
}
