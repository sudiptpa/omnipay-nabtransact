<?php

namespace Omnipay\NABTransact\Exception;

class InvalidFingerprintException extends \Exception implements \Omnipay\Common\Exception\OmnipayException
{
    private $data = [];

    public function __construct($message, $data = [])
    {
        $this->data = $data;

        parent::__construct($message);
    }

    public function getData()
    {
        return $this->data;
    }
}
