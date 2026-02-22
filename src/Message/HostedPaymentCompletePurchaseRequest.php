<?php

namespace Omnipay\NABTransact\Message;

/**
 * HostedPayment Complete Purchase Request.
 */
class HostedPaymentCompletePurchaseRequest extends AbstractRequest
{
    /**
     * @return array
     */
    public function getData()
    {
        $query = $this->httpRequest->query->all();
        if (!empty($query)) {
            return $query;
        }

        $request = $this->httpRequest->request->all();
        if (!empty($request)) {
            return $request;
        }

        return [];
    }

    /**
     * @param array $data
     *
     * @return HostedPaymentCompletePurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new HostedPaymentCompletePurchaseResponse($this, $data);
    }
}
