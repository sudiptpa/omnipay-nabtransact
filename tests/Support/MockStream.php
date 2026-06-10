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

    public function __toString(): string
    {
        return $this->content;
    }

    public function close(): void
    {
        // no-op
    }

    public function detach()
    {
        return null;
    }

    public function getSize(): ?int
    {
        return strlen($this->content);
    }

    public function tell(): int
    {
        return $this->position;
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->content);
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $offset = (int) $offset;
        $whence = (int) $whence;

        if ($whence === SEEK_SET) {
            $this->position = max(0, $offset);

            return;
        }

        if ($whence === SEEK_CUR) {
            $this->position = max(0, $this->position + $offset);

            return;
        }

        if ($whence === SEEK_END) {
            $this->position = max(0, strlen($this->content) + $offset);
        }
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write($string): int
    {
        $string = (string) $string;

        $prefix = substr($this->content, 0, $this->position);
        $suffixStart = $this->position + strlen($string);
        $suffix = '';

        if ($suffixStart < strlen($this->content)) {
            $suffix = substr($this->content, $suffixStart);
        }

        $this->content = $prefix . $string . $suffix;
        $this->position += strlen($string);

        return strlen($string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read($length): string
    {
        $length = max(0, (int) $length);

        if ($length === 0 || $this->eof()) {
            return '';
        }

        $chunk = substr($this->content, $this->position, $length);
        $this->position += strlen($chunk);

        return $chunk;
    }

    public function getContents(): string
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
