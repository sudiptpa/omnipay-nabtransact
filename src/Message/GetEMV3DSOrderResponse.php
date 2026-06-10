<?php

namespace Omnipay\NABTransact\Message;

/**
 * NABTransact Get EMV 3DS Order Response.
 */
class GetEMV3DSOrderResponse extends EMV3DSOrderResponse
{
    public function getTransStatus()
    {
        $data = $this->data;

        return isset($data['threedSecure']['transStatus'])
            ? $data['threedSecure']['transStatus']
            : null;
    }

    public function getEci()
    {
        $data = $this->data;

        return isset($data['threedSecure']['eci'])
            ? $data['threedSecure']['eci']
            : null;
    }

    public function getLiabilityShiftIndicator()
    {
        $data = $this->data;

        return isset($data['threedSecure']['liabilityShiftIndicator'])
            ? $data['threedSecure']['liabilityShiftIndicator']
            : null;
    }
}
