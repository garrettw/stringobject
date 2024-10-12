<?php

namespace StringObject;

use LogicException;
use InvalidArgumentException;

abstract class AbstractString implements StringObject
{
    // PROPERTIES

    protected $raw;
    protected $caret = 0;
    protected $token = false;

    /**
     * @param mixed $thing Anything that can be cast to a string
     */
    final public function __construct($thing)
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

    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Cannot set ' . $value . ' on immutable StringObject at offset ' . $offset);
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('Cannot unset character at offset ' . $offset . ' on immutable StringObject');
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
        return $this->count();
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
        return new static($this->raw . $str);
    }

    public function concat($str): static
    {
        return $this->append($str);
    }

    public function escape(int $mode = self::NORMAL, string $charlist = ''): static
    {
        // strip out bits we don't understand
        $mode &= (self::C_STYLE | self::META);

        $modesmap = [
            self::NORMAL => 'addslashes',
            self::C_STYLE => 'addcslashes',
            self::META => 'quotemeta',
        ];
        if ($mode === self::C_STYLE) {
            return new static(\call_user_func($modesmap[$mode], $this->raw, $charlist));
        }
        return new static(\call_user_func($modesmap[$mode], $this->raw));
    }

    public function hexDecode(): static
    {
        return new static(\hex2bin($this->raw));
    }

    public function hexEncode(): static
    {
        return new static(\bin2hex($this->raw));
    }

    public function prepend(string $str): static
    {
        return new static($str . $this->raw);
    }

    public function remove(string $str, $mode = self::NORMAL): static
    {
        return $this->replace($str, '', $mode);
    }

    public function removeSubstr(int $start, int $length = null): static
    {
        return $this->replaceSubstr('', $start, $length);
    }

    public function repeat(int $times): static
    {
        return new static(\str_repeat($this->raw, $times));
    }

    public function replace(string $search, string $replace, int $mode = self::NORMAL): static
    {
        if ($mode & self::CASE_INSENSITIVE) {
            return new static(\str_ireplace($search, $replace, $this->raw));
        }
        return new static(\str_replace($search, $replace, $this->raw));
    }

    public function translate(mixed $search, string $replace = ''): static
    {
        if (is_array($search)) {
            return new static(\strtr($this->raw, $search));
        }
        return new static(\strtr($this->raw, $search, $replace));
    }

    public function trim(string $mask = " \t\n\r\0\x0B", int $mode = self::BOTH_ENDS): static
    {
        // strip out bits we don't understand
        $mode &= (self::END | self::BOTH_ENDS);

        $modesmap = [
            self::START => 'ltrim',
            self::END => 'rtrim',
            self::BOTH_ENDS => 'trim',
        ];
        return new static(\call_user_func($modesmap[$mode], $this->raw, $mask));
    }

    public function unescape(int $mode = self::NORMAL): static
    {
        // strip out bits we don't understand
        $mode &= (self::C_STYLE | self::META);

        $modesmap = [
            self::NORMAL => 'stripslashes',
            self::C_STYLE => 'stripcslashes',
            self::META => 'stripslashes',
        ];
        return new static(\call_user_func($modesmap[$mode], $this->raw));
    }

    public function nextToken(string $delim): static
    {
        if ($this->token) {
            return new static(\strtok($delim));
        }
        $this->token = true;
        return new static(\strtok($this->raw, $delim));
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
        return new static(\convert_uudecode($this->raw));
    }

    public function uuEncode(): static
    {
        return new static(\convert_uuencode($this->raw));
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
        throw new InvalidArgumentException('Parameter is not stringable');
    }
}
