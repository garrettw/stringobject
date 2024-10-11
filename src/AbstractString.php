<?php

namespace StringObject;

abstract class AbstractString implements \ArrayAccess, \Countable, \Iterator, StringObject
{
    // PROPERTIES

    protected $raw;
    protected $caret = 0;
    protected $token = false;

    /**
     * @param mixed $thing Anything that can be cast to a string
     */
    public function __construct($thing)
    {
        static::stringableOrFail($thing);
        $this->raw = (string) $thing;
    }

    public function __get(string $name): mixed
    {
        return $this->$name;
    }

    public function __toString(): string
    {
        return $this->raw;
    }

    // ArrayAccess methods {

    public function offsetExists($offset): bool
    {
        $offset = (int) $offset;
        return ($offset >= 0 && $offset < $this->count());
    }

    // END ArrayAccess methods }

    // Iterator methods {

    public function key(): mixed
    {
        return $this->caret;
    }

    public function next(): void
    {
        $this->caret++;
    }

    public function rewind(): void
    {
        $this->caret = 0;
    }

    public function valid(): bool
    {
        return ($this->caret < $this->count());
    }

    // END Iterator methods }

    public function equals($str): bool
    {
        static::stringableOrFail($str);

        $str = (string) $str;
        return ($str == $this->raw);
    }

    public function length(): int
    {
        return \strlen($this->raw);
    }

    public function charCodeAt(int $offset): int
    {
        return \ord($this->raw[$offset]);
    }

    public function isAscii(): bool
    {
        $len = $this->length();

        for ($i = 0; $i < $len; $i++) {
            if ($this->charCodeAt($i) >= 128) {
                return false;
            }
        }
        return true;
    }

    public function isEmpty(): bool
    {
        return empty($this->raw);
    }

    public function append($str): static
    {
        return $this->replaceWhole($this->raw . $str);
    }

    public function concat($str): static
    {
        return $this->append($str);
    }

    public function hexDecode(): static
    {
        return $this->replaceWhole(\hex2bin($this->raw));
    }

    public function hexEncode(): static
    {
        return $this->replaceWhole(\bin2hex($this->raw));
    }

    public function nextToken(string $delim): static
    {
        if ($this->token) {
            return new static(\strtok($delim));
        }
        $this->token = true;
        return new static(\strtok($this->raw, $delim));
    }

    public function replaceWhole(string $replacement = ''): static
    {
        return new static($replacement);
    }

    public function resetToken(): void
    {
        $this->token = false;
    }

    public function times(int $times): static
    {
        return $this->repeat($times);
    }

    public function uuDecode(): static
    {
        return $this->replaceWhole(\convert_uudecode($this->raw));
    }

    public function uuEncode(): static
    {
        return $this->replaceWhole(\convert_uuencode($this->raw));
    }

    protected static function stringableOrFail($thing): bool
    {
        if (
            is_string($thing)
            || (\is_object($thing) && \method_exists($thing, '__toString'))
        ) {
            return true;
        }

        // return false;
        throw new \InvalidArgumentException('Parameter is not stringable');
    }
}
