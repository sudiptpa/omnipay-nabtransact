<?php

namespace Omnipay\NABTransact;

use Omnipay\NABTransact\Message\UnionPayCompletePurchaseRequest;
use Omnipay\NABTransact\Message\UnionPayPurchaseRequest;

/**
 * NABTransact UnionPay Gateway.
 */
class UnionPayGateway extends DirectPostGateway
{
    public function getName()
    {
        return 'NAB Transact UnionPay';
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\UnionPayPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        $request = $this->createRequest(UnionPayPurchaseRequest::class, $parameters);
        /** @var UnionPayPurchaseRequest $request */

        return $request;
    }

    /**
     * @param array $parameters
     *
     * @return \Omnipay\NABTransact\Message\UnionPayCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        $request = $this->createRequest(UnionPayCompletePurchaseRequest::class, $parameters);
        /** @var UnionPayCompletePurchaseRequest $request */

        return $request;
    }
}
