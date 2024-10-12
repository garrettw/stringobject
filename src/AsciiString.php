<?php

namespace StringObject;

class AsciiString extends AbstractString
{
    public function toArray(string $delim = '', int $limit = null): array
    {
        if (empty($delim)) {
            return \str_split($this->raw);
        }
        if (is_int($delim)) {
            return \str_split($this->raw, $delim);
        }
        if ($limit === null) {
            return \explode($delim, $this->raw);
        }
        return \explode($delim, $this->raw, $limit);
    }

    // INFORMATIONAL METHODS

    public function charAt(int $offset): static
    {
        return new static($this->raw[$offset]);
    }

    public function compareTo(string $str, int $mode = self::NORMAL, int $length = 1): mixed
    {
        // strip out bits we don't understand
        $mode &= (self::CASE_INSENSITIVE | self::CURRENT_LOCALE | self::NATURAL_ORDER | self::FIRST_N);

        $modesmap = [
            self::NORMAL => 'strcmp',
            self::CASE_INSENSITIVE => 'strcasecmp',
            self::CURRENT_LOCALE => 'strcoll',
            self::NATURAL_ORDER => 'strnatcmp',
            (self::NATURAL_ORDER | self::CASE_INSENSITIVE) => 'strnatcasecmp',
            self::FIRST_N => 'strncmp',
            (self::FIRST_N | self::CASE_INSENSITIVE) => 'strncasecmp',
        ];

        if ($mode & self::FIRST_N) {
            return \call_user_func($modesmap[$mode], $this->raw, $str, $length);
        }
        return \call_user_func($modesmap[$mode], $this->raw, $str);
    }

    public function indexOf(string $needle, int $offset = 0, int $mode = self::NORMAL): mixed
    {
        // strip out bits we don't understand
        $mode &= (self::REVERSE | self::CASE_INSENSITIVE);

        $modesmap = [
            self::NORMAL => 'strpos',
            self::CASE_INSENSITIVE => 'stripos',
            self::REVERSE => 'strrpos',
            (self::REVERSE | self::CASE_INSENSITIVE) => 'strripos',
        ];
        return \call_user_func($modesmap[$mode], $this->raw, $needle, $offset);
    }

    // MODIFYING METHODS

    public function chunk(int $length = 76, string $ending = "\r\n"): static
    {
        return new static(\chunk_split($this->raw, $length, $ending));
    }

    public function insertAt(string $str, int $offset): static
    {
        return $this->replaceSubstr($str, $offset, 0);
    }

    public function pad(int $length, string $pad_string = ' ', $pad_type = self::END)
    {
        return new static(\str_pad($this->raw, $length, $pad_string, $pad_type));
    }

    public function reverse(): static
    {
        return new static(\strrev($this->raw));
    }

    public function shuffle(): static
    {
        return new static(\str_shuffle($this->raw));
    }

    public function substr(int $start, int $length = null): static
    {
        return new static(\substr($this->raw, $start, $length));
    }

    // TESTING METHODS

    public function contains(string $needle, int $offset = 0, int $mode = self::NORMAL): bool
    {
        if ($mode & self::EXACT_POSITION) {
            return ($this->indexOf($needle, $offset, $mode) === $offset);
        }
        return ($this->indexOf($needle, $offset, $mode) !== false);
    }

    public function countSubstr(string $needle, int $offset = 0, int $length = null): int
    {
        if ($length === null) {
            return \substr_count($this->raw, $needle, $offset);
        }
        return \substr_count($this->raw, $needle, $offset, $length);
    }

    public function endsWith(string $str, int $mode = self::NORMAL): bool
    {
        $mode &= self::CASE_INSENSITIVE;
        $offset = $this->length() - \strlen($str);
        return $this->contains($str, $offset, $mode | self::EXACT_POSITION | self::REVERSE);
    }

    public function startsWith(string $str, int $mode = self::NORMAL): bool
    {
        $mode &= self::CASE_INSENSITIVE;
        return $this->contains($str, 0, $mode | self::EXACT_POSITION);
    }

    // INTERFACE IMPLEMENTATION METHODS

    public function count(): int
    {
        return \strlen($this->raw);
    }

    public function current(): string
    {
        return $this->raw[$this->caret];
    }

    public function offsetGet($offset): string
    {
        return $this->raw[$offset];
    }
}
