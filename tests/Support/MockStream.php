<?php

declare(strict_types=1);

namespace Omnipay\NABTransact\Tests\Support;

use Psr\Http\Message\StreamInterface;

final class MockStream implements StreamInterface
{
    private string $content;

    private int $position = 0;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function close()
    {
        // no-op
    }

    public function detach()
    {
        return null;
    }

    public function getSize()
    {
        return strlen($this->content);
    }

    public function tell()
    {
        return $this->position;
    }

    public function eof()
    {
        return $this->position >= strlen($this->content);
    }

    public function isSeekable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if ($whence === SEEK_SET) {
            $this->position = max(0, (int) $offset);

            return;
        }

        if ($whence === SEEK_CUR) {
            $this->position = max(0, $this->position + (int) $offset);

            return;
        }

        if ($whence === SEEK_END) {
            $this->position = max(0, strlen($this->content) + (int) $offset);
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function isWritable()
    {
        return true;
    }

    public function write($string)
    {
        $string = (string) $string;

        $prefix = substr($this->content, 0, $this->position);
        $suffixStart = $this->position + strlen($string);
        $suffix = '';

        if ($suffixStart < strlen($this->content)) {
            $suffix = substr($this->content, $suffixStart);
        }

        $this->content = $prefix.$string.$suffix;
        $this->position += strlen($string);

        return strlen($string);
    }

    public function isReadable()
    {
        return true;
    }

    public function read($length)
    {
        $length = max(0, (int) $length);

        if ($length === 0 || $this->eof()) {
            return '';
        }

        $chunk = substr($this->content, $this->position, $length);
        $this->position += strlen($chunk);

        return $chunk;
    }

    public function getContents()
    {
        if ($this->eof()) {
            return '';
        }

        $chunk = substr($this->content, $this->position);
        $this->position = strlen($this->content);

        return $chunk;
    }

    public function getMetadata($key = null)
    {
        if ($key === null) {
            return [];
        }

        return null;
    }
}
