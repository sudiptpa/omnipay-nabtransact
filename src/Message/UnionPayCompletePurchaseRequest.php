<?php

namespace Omnipay\NABTransact\Message;

/**
 * UnionPayCompletePurchaseRequest
 */
class UnionPayCompletePurchaseRequest extends DirectPostCompletePurchaseRequest
{
    /**
     * @param $data
     * @return mixed
     */
    public function sendData($data)
    {
        return $this->response = new UnionPayCompletePurchaseResponse($this, $data);
    }
}
