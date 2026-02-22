<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * DirectPost server-to-server operation response.
 */
class DirectPostApiResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        $code = $this->getCode();

        if ($code !== null) {
            return in_array($code, ['00', '08', '11'], true);
        }

        $statusCode = $this->getHttpStatusCode();

        if ($statusCode === null) {
            return false;
        }

        return $statusCode >= 200 && $statusCode <= 299;
    }

    /**
     * @return int|null
     */
    public function getHttpStatusCode()
    {
        if (isset($this->data['http_status_code'])) {
            return (int) $this->data['http_status_code'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        foreach (['rescode', 'responsecode', 'statuscode', 'code'] as $key) {
            if (isset($this->data[$key])) {
                return (string) $this->data[$key];
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        foreach (['restext', 'responsetext', 'statusdescription', 'message', 'error'] as $key) {
            if (isset($this->data[$key])) {
                return (string) $this->data[$key];
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        foreach (['txnid', 'transactionid', 'eps_txnid'] as $key) {
            if (isset($this->data[$key])) {
                return (string) $this->data[$key];
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getRawResponse()
    {
        if (isset($this->data['raw'])) {
            return (string) $this->data['raw'];
        }

        return null;
    }
}
