<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Exception\InvalidFingerprintException;

/**
 * NABTransact Direct Post Complete Purchase Request.
 */
class DirectPostCompletePurchaseRequest extends DirectPostAbstractRequest
{
    public function getData()
    {
        $data = $this->httpRequest->query->all();

        $received_fingerprint = $this->httpRequest->query->get('fingerprint');

        $generated_fingerprint = $this->generateResponseFingerprint($data);

        if ($generated_fingerprint !== $received_fingerprint) {
            throw new InvalidFingerprintException('Invalid fingerprint', $data);
        }

        return $data;
    }

    public function generateResponseFingerprint($data)
    {
        $hashable = [
            $data['merchant'],
            $this->getTransactionPassword(),
            $data['refid'],
            $this->getAmount(),
            $data['timestamp'],
            $data['summarycode'],
        ];

        $hash = implode('|', $hashable);

        return hash_hmac('sha256', $hash, $this->getTransactionPassword());
    }

    public function sendData($data)
    {
        return $this->response = new DirectPostCompletePurchaseResponse($this, $data);
    }
}
