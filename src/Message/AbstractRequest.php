<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\NABTransact\Transport\TransportInterface;

/**
 * NABTransact Abstract Request.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    public $testEndpoint;

    public $liveEndpoint;

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getTransactionPassword()
    {
        return $this->getParameter('transactionPassword');
    }

    public function setTransactionPassword($value)
    {
        return $this->setParameter('transactionPassword', $value);
    }

    public function getHasEMV3DSEnabled()
    {
        return $this->getParameter('hasEMV3DSEnabled');
    }

    public function setHasEMV3DSEnabled($value)
    {
        return $this->setParameter('hasEMV3DSEnabled', $value);
    }

    public function getHasRiskManagementEnabled()
    {
        return $this->getParameter('hasRiskManagementEnabled');
    }

    public function setHasRiskManagementEnabled($value)
    {
        return $this->setParameter('hasRiskManagementEnabled', $value);
    }

    /**
     * @param TransportInterface $transport
     *
     * @return $this
     */
    public function setTransport(TransportInterface $transport)
    {
        return $this->setParameter('transport', $transport);
    }

    /**
     * @return TransportInterface|null
     */
    public function getTransport()
    {
        return $this->getParameter('transport');
    }

    /**
     * @return int
     */
    public function getTimeoutSeconds()
    {
        $timeout = $this->getParameter('timeoutSeconds');

        if ($timeout === null) {
            return 60;
        }

        return max(1, (int) $timeout);
    }

    public function setTimeoutSeconds($value)
    {
        return $this->setParameter('timeoutSeconds', (int) $value);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
