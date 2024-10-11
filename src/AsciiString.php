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

    public function compareTo(string $str, int $mode = self::NORMAL, int $length = 1): int
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
        return $this->replaceWhole(\chunk_split($this->raw, $length, $ending));
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
            return $this->replaceWhole(\call_user_func($modesmap[$mode], $this->raw, $charlist));
        }
        return $this->replaceWhole(\call_user_func($modesmap[$mode], $this->raw));
    }

    public function insertAt(string $str, int $offset): static
    {
        return $this->replaceSubstr($str, $offset, 0);
    }

    public function pad(int $length, string $pad_string = ' ', $pad_type = self::END)
    {
        return $this->replaceWhole(\str_pad($this->raw, $length, $pad_string, $pad_type));
    }

    public function prepend(string $str): static
    {
        return $this->replaceWhole($str . $this->raw);
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
        return $this->replaceWhole(\str_repeat($this->raw, $times));
    }

    public function replace(string $search, string $replace, int $mode = self::NORMAL): static
    {
        if ($mode & self::CASE_INSENSITIVE) {
            return $this->replaceWhole(\str_ireplace($search, $replace, $this->raw));
        }
        return $this->replaceWhole(\str_replace($search, $replace, $this->raw));
    }

    public function replaceSubstr(string $replacement, int $start, int $length = null): static
    {
        if ($length === null) {
            $length = $this->length();
        }
        return $this->replaceWhole(\substr_replace($this->raw, $replacement, $start, $length));
    }

    public function reverse(): static
    {
        return $this->replaceWhole(\strrev($this->raw));
    }

    public function shuffle(): static
    {
        return $this->replaceWhole(\str_shuffle($this->raw));
    }

    public function substr(int $start, mixed $length = 'omitted'): static
    {
        if ($length === 'omitted') {
            return new static(\substr($this->raw, $start));
        }
        return new static(\substr($this->raw, $start, $length));
    }

    public function translate(string $search, string $replace = ''): static
    {
        if (is_array($search)) {
            return $this->replaceWhole(\strtr($this->raw, $search));
        }
        return $this->replaceWhole(\strtr($this->raw, $search, $replace));
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
        return $this->replaceWhole(\call_user_func($modesmap[$mode], $this->raw, $mask));
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
        return $this->replaceWhole(\call_user_func($modesmap[$mode], $this->raw));
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

    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('Cannot assign ' . $value . ' to immutable AsciiString instance at index ' . $offset);
    }

    public function offsetUnset($offset): void
    {
        throw new \LogicException('Cannot unset index ' . $offset . ' on immutable AsciiString instance');
    }
}
