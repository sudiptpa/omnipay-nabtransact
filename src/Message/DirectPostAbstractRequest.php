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

    /**
     * @param array $data
     */
    public function generateFingerprint(array $data)
    {
        $hash = implode(
            '|',
            array(
                $data['EPS_MERCHANT'],
                $this->getTransactionPassword(),
                $data['EPS_TXNTYPE'],
                $data['EPS_REFERENCEID'],
                $data['EPS_AMOUNT'],
                $data['EPS_TIMESTAMP'],
            )
        );

        return sha1($hash);
    }
}
