<?php

namespace Omnipay\NABTransact\Message;

class DirectPostWebhookRequest extends DirectPostAbstractRequest
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function generateResponseFingerprint($data)
    {
        $hashable = [
            $data['merchant'],
            $data['txn_password'],
            $data['refid'],
            $data['amount'],
            $data['timestamp'],
            $data['summarycode'],
        ];

        $hash = implode('|', $hashable);

        return hash_hmac('sha256', $hash, $data['txn_password']);
    }

    public function verifyFingerPrint($fingerprint)
    {
        $data = $this->data;

        if ($fingerprint !== $this->generateResponseFingerprint($data)) {
            $existing = isset($data['restext']) ? trim((string) $data['restext']) : '';
            $data['restext'] = $existing === '' ? 'Invalid fingerprint.' : $existing.', Invalid fingerprint.';
            $data['summarycode'] = 3;
        }

        return $this->response = new DirectPostCompletePurchaseResponse($this, $data);
    }

    /**
     * Backward-compatible typo alias.
     *
     * @deprecated Use verifyFingerPrint().
     */
    public function vefiyFingerPrint($fingerprint)
    {
        return $this->verifyFingerPrint($fingerprint);
    }

    public function getData()
    {
        return $this->data;
    }

    public function sendData($data)
    {
        $fingerprint = isset($data['fingerprint']) ? $data['fingerprint'] : '';

        return $this->verifyFingerPrint($fingerprint);
    }
}
