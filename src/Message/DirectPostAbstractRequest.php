<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Direct Post Abstract Request.
 */
abstract class DirectPostAbstractRequest extends AbstractRequest
{
    /**
     * @var string
     */
    public $testEndpoint = 'https://transact.nab.com.au/test/directpostv2/authorise';

    /**
     * @var string
     */
    public $liveEndpoint = 'https://transact.nab.com.au/live/directpostv2/authorise';
}
