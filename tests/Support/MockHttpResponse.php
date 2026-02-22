<?php

declare(strict_types=1);

namespace Omnipay\NABTransact\Tests\Support;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class MockHttpResponse implements ResponseInterface
{
    private string $protocolVersion;

    /** @var array<string, array<int, string>> */
    private array $headers;

    private StreamInterface $body;

    private int $statusCode;

    private string $reasonPhrase;

    /** @var array<int, string> */
    private static $defaultPhrases = [
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    /**
     * @param array<string, array<int, string>|string> $headers
     */
    public function __construct(
        int $statusCode = 200,
        array $headers = [],
        $body = '',
        string $protocolVersion = '1.1',
        string $reasonPhrase = ''
    )
    {
        $this->statusCode = $statusCode;
        $this->headers = [];

        foreach ($headers as $name => $value) {
            $values = is_array($value) ? $value : [(string) $value];
            $this->headers[strtolower($name)] = array_values(array_map('strval', $values));
        }

        $this->body = $body instanceof StreamInterface ? $body : new MockStream((string) $body);
        $this->protocolVersion = $protocolVersion;
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $clone = clone $this;
        $clone->protocolVersion = (string) $version;

        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return array_key_exists(strtolower((string) $name), $this->headers);
    }

    public function getHeader($name)
    {
        $key = strtolower((string) $name);

        return $this->headers[$key] ?? [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $values = is_array($value) ? $value : [(string) $value];
        $clone->headers[strtolower((string) $name)] = array_values(array_map('strval', $values));

        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $key = strtolower((string) $name);
        $values = is_array($value) ? $value : [(string) $value];

        if (!isset($clone->headers[$key])) {
            $clone->headers[$key] = [];
        }

        $clone->headers[$key] = array_merge($clone->headers[$key], array_values(array_map('strval', $values)));

        return $clone;
    }

    public function withoutHeader($name)
    {
        $clone = clone $this;
        unset($clone->headers[strtolower((string) $name)]);

        return $clone;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $clone = clone $this;
        $clone->statusCode = (int) $code;
        $clone->reasonPhrase = (string) $reasonPhrase;

        return $clone;
    }

    public function getReasonPhrase()
    {
        if ($this->reasonPhrase !== '') {
            return $this->reasonPhrase;
        }

        return self::$defaultPhrases[$this->statusCode] ?? '';
    }
}
