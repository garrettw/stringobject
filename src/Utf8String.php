<?php

namespace StringObject;

use BadMethodCallException;
use StringObject\Utf8\Parser;
use StringObject\Utf8\ToAscii;

class Utf8String extends AbstractString
{
    const RAW = 0;
    const NFC = 1;
    const NFD = 2;
    const NFK = 4;
    const NFKC = 5;
    const NFKD = 6;

    /** @var array<int, array<int, string|int>> */
    protected array $chars = [];
    protected mixed $uhandler;
    protected int $normform = self::RAW;

    /**
     * @param int|string $delim
     * @return array<int, mixed>
     */
    public function toArray($delim = '', ?int $limit = null): array
    {
        $this->parse();

        if (empty($delim)) {
            return array_column($this->chars, 0);
        }
        if (is_int($delim)) {
            return \str_split($this->raw, abs($delim));
        }
        return \explode($delim, $this->raw, $limit ?? PHP_INT_MAX);
    }

    public function charAt(int $index): string
    {
        $this->parse();
        return (string) $this->chars[$index][0];
    }

    public function charCodeAt(int $index): int
    {
        $this->parse();
        return (int) $this->chars[$index][1];
    }

    /**
     * Use mbstring ideally but implement a PHP fallback
     */
    public function chunk(int $length = 76, string $ending = "\r\n"): static
    {
        throw new BadMethodCallException('chunk() not implemented yet');
    }

    /**
     * intl or bust.
     */
    public function detectForm(): void
    {
    }

    /**
     * intl. PHP fallback could compare codepoints but it's not the same.
     */
    public function compareTo(string $str, int $mode = self::NORMAL, int $length = 1): mixed
    {
        if (!extension_loaded('intl')) {
            throw new BadMethodCallException('intl extension is required to compare Unicode strings');
        }

        $coll = new \Collator('');
        if ($mode !== self::NORMAL) {
            $coll->setStrength($mode);
        }
        return $coll->compare($this->raw, $str);
    }

    public function count(): int
    {
        $this->parse();
        return \count($this->chars);
    }

    /**
     * @return string
     */
    public function current(): string
    {
        $this->parse();
        return (string) $this->chars[$this->caret][0];
    }
    
    /**
     * Use mbstring ideally but implement a PHP fallback
     */
    public function indexOf(string $needle, int $offset = 0, int $mode = self::NORMAL): mixed
    {
        if (!extension_loaded('mbstring')) {
            throw new BadMethodCallException('mbstring extension is required to search a Unicode string');
        }

        // strip out bits we don't understand
        $mode &= (self::REVERSE | self::CASE_INSENSITIVE);

        $modesmap = [
            self::NORMAL => 'mb_strpos',
            self::CASE_INSENSITIVE => 'mb_stripos',
            self::REVERSE => 'mb_strrpos',
            (self::REVERSE | self::CASE_INSENSITIVE) => 'mb_strripos',
        ];
        return \call_user_func($modesmap[$mode], $this->raw, $needle, $offset);
    }

    public function offsetGet($offset): string
    {
        $this->parse();
        return (string) $this->chars[$offset][0];
    }
    
    /**
     * Use mbstring ideally but implement a PHP fallback
     */
    public function pad(int $length, string $padString = ' ', int $padType = self::END): static
    {
        throw new BadMethodCallException('pad() not implemented yet');
    }

    public function substr(int $start, int $length = null): static
    {
        $this->parse();
        return new static(\implode('', \array_slice(array_column($this->chars, 0), $start, $length)));
    }

    /**
     * intl or bust. Unless I figure out a way to do it.
     */
    public function normalize(Normalize\Normalizer $norm): static
    {
        $this->parse();
        $result = $norm->normalize($this);

        if (is_int($result)) {
            // it was already normalized
            $this->normform = $result;
            return $this;
        }
        return new static($result);
    }

    public function toAscii(bool $allow1252 = false): AsciiString|Win1252String
    {
        return ToAscii::transliterate($this->__toString(), $allow1252);
    }

    /**
     * Use mbstring ideally but implement a PHP fallback
     */
    public function replaceSubstr(string $replacement, int $start, ?int $length = null): static
    {
        throw new BadMethodCallException('replaceSubstr() is not implemented yet');
    }

    protected function parse(): void
    {
        if (!empty($this->chars)) {
            return;
        }
        $this->chars = Parser::parse($this->raw);
    }
}
