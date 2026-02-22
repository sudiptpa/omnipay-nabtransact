<?php

namespace Omnipay\NABTransact\Transport;

final class TransportResponse
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array<string,string>
     */
    private $headers;

    /**
     * @param int                  $statusCode
     * @param string               $body
     * @param array<string,string> $headers
     */
    public function __construct($statusCode, $body, array $headers = [])
    {
        $this->statusCode = (int) $statusCode;
        $this->body = (string) $body;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array<string,string>
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
