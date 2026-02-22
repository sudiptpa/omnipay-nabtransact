<?php

namespace Omnipay\NABTransact\Transport;

interface TransportInterface
{
    /**
     * @param string               $method
     * @param string               $url
     * @param array<string,string> $headers
     * @param string               $body
     * @param int                  $timeoutSeconds
     *
     * @return TransportResponse
     */
    public function send($method, $url, array $headers = [], $body = '', $timeoutSeconds = 60);
}
