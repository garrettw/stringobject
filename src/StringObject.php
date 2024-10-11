<?php

namespace StringObject;

interface StringObject
{
    const START = 0;
    const END = 1;
    const BOTH_ENDS = 2;
    const NORMAL = 0;
    const CASE_INSENSITIVE = 1;
    const REVERSE = 2;
    const EXACT_POSITION = 4;
    const CURRENT_LOCALE = 2;
    const NATURAL_ORDER = 4;
    const FIRST_N = 8;
    const C_STYLE = 1;
    const META = 2;
    const GREEDY = 0;
    const LAZY = 1;

    public function __toString(): string;
    public function toArray(string $delim = '', int $limit = null): array;
    public function append(string $str): static;
    public function compareTo(string $str, int $mode = self::NORMAL, int $length = 1): int;
    public function escape(int $mode = self::NORMAL, string $charlist = ''): static;
    public function length(): int;
    public function prepend(string $str): static;
    public function remove(string $str, $mode = self::NORMAL): static;
    public function removeSubstr(int $start, int $length = null): static;
    public function repeat(int $times): static;
    public function replace(string $search, string $replace, int $mode = self::NORMAL): static;
    public function replaceSubstr(string $replacement, int $start, int $length = null): static;
    public function replaceWhole(string $replacement = ''): static;
    public function substr(int $start, mixed $length = 'omitted'): static;
    public function translate(string $search, string $replace = ''): static;
    public function trim(string $mask = " \t\n\r\0\x0B", int $mode = self::BOTH_ENDS): static;
    public function unescape(int $mode = self::NORMAL): static;
}
