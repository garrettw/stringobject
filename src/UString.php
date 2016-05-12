<?php

namespace StringObject;

class UString extends AnyString
{
    const NOT_NORMALIZED = 0;
    const NFC = 1;
    const NFD = 2;
    const NFK = 4;
    const NFKC = 5;
    const NFKD = 6;

    protected $chars = [];
    protected $uhandler;
    protected $normform = self::NOT_NORMALIZED;

    protected static $spec = [
        2 => ['mask' => 0b00011111, 'start' => 0x80],
        3 => ['mask' => 0b00001111, 'start' => 0x800],
        4 => ['mask' => 0b00000111, 'start' => 0x10000],
        5 => ['mask' => 0b00000011, 'start' => 0x200000],
        6 => ['mask' => 0b00000001, 'start' => 0x4000000],
    ];
    protected static $winc1umap = [
        0x80 => 0x20AC,
        0x81 => 0xFFFD, // invalid
        0x82 => 0x201A,
        0x83 => 0x0192,
        0x84 => 0x201E,
        0x85 => 0x2026,
        0x86 => 0x2020,
        0x87 => 0x2021,
        0x88 => 0x02C6,
        0x89 => 0x2030,
        0x8A => 0x0160,
        0x8B => 0x2039,
        0x8C => 0x0152,
        0x8D => 0xFFFD, // invalid
        0x8E => 0x017D,
        0x8F => 0xFFFD, // invalid
        0x90 => 0xFFFD, // invalid
        0x91 => 0x2018,
        0x92 => 0x2019,
        0x93 => 0x201C,
        0x94 => 0x201D,
        0x95 => 0x2022,
        0x96 => 0x2013,
        0x97 => 0x2014,
        0x98 => 0x02DC,
        0x99 => 0x2122,
        0x9A => 0x0161,
        0x9B => 0x203A,
        0x9C => 0x0153,
        0x9D => 0xFFFD, // invalid
        0x9E => 0x017E,
        0x9F => 0x0178,
    ];

    public function toArray($delim = '', $limit = null)
    {
        $this->parse();

        if (empty($delim)) {
            return $this->chars;
        }
        if (is_int($delim)) {
            return \str_split($this->raw, $delim);
        }
        if ($limit === null) {
            return \explode($delim, $this->raw);
        }
        return \explode($delim, $this->raw, $limit);
    }

    /**
     * @return string
     */
    public function charAt($index)
    {
        $this->parse();
        return $this->chars[$index][0];
    }

    /**
     * @return int
     */
    public function charCodeAt($index)
    {
        $this->parse();
        return $this->chars[$index][1];
    }

    public function detectForm()
    {

    }

    public function length()
    {
        $this->parse();
        return \count($this->chars);
    }

    public function normalize($target = self::NFC)
    {

    }

    /**
     *
     */
    protected static function cpToUtf8Char($cpt)
    {
        if ($cpt < self::$spec[2]['start']) {
            return \chr($cpt);
        }

        if ($cpt == 0xFEFF) {
            return '';
        }

        if (($cpt >= 0xD800 && $cpt <= 0xDFFF) || $cpt > 0x10FFFF) {
            return "\xEF\xBF\xBD"; // U+FFFD; invalid symbol
        }

        if ($cpt < self::$spec[3]['start']) {
            $data = [
                0b11000000 | ($cpt >> 6),
                0b10000000 | ($cpt & 0b00111111)
            ];
        } elseif ($cpt < self::$spec[4]['start']) {
            $data = [
                0b11100000 | ($cpt >> 12),
                0b10000000 | (($cpt >> 6) & 0b00111111),
                0b10000000 | ($cpt & 0b00111111),
            ];
        } else {
            $data = [
                0b11110100,
                0b10000000 | (($cpt >> 12) & 0b00111111),
                0b10000000 | (($cpt >> 6) & 0b00111111),
                0b10000000 | ($cpt & 0b00111111),
            ];
        }

        return implode(array_map('chr', $data));
    }
    /**
     * @param integer $byte
     */
    protected static function charLength($byte)
    {
        if (($byte & 0b11111110) === 0b11111100) {
            return 6;
        }
        if (($byte & 0b11111100) === 0b11111000) {
            return 5;
        }
        if (($byte & 0b11111000) === 0b11110000) {
            return 4;
        }
        if (($byte & 0b11110000) === 0b11100000) {
            return 3;
        }
        if (($byte & 0b11100000) === 0b11000000) {
            return 2;
        }
        return 1;
    }

    private function parse()
    {
        if (!empty($this->chars)) {
            return;
        }

        $len = \strlen($this->raw);
        $inside = false; // are we "inside" of evaluating a valid UTF-8 char?
        $invalid = false;

        for ($offset = 0; $offset < $len; $offset++) {
            $char = $this->raw{$offset};
            $ord = \ord($char);

            if ($inside === false) {
                $bytes = self::charLength($ord);

                if ($bytes > 1 && $offset + $bytes <= $len && $invalid === false) {
                    // valid UTF-8 multibyte start
                    $inside = true;
                    $cache = $char;
                    $ordcache = ($ord & self::$spec[$bytes]['mask']) << (6 * ($bytes - 1));
                    $originOffset = $offset;
                } elseif ($ord < self::$spec[2]['start']) {
                    // ASCII 7-bit char
                    $this->chars[] = [$char, $ord];
                } else {
                    // either C0/C1 block or higher; map from cp1252 to utf8 or just convert
                    $ord = (isset(self::$winc1umap[$ord])) ? self::$winc1umap[$ord] : $ord;
                    $this->chars[] = [self::cpToUtf8Char($ord), $ord];
                    $invalid = false;
                }
                continue;
            }

            // $inside === true, i.e. *should be* continuation character
            if (($ord & 0b11000000) !== 0b10000000) {
                // actually, it's not one, so now the whole UTF-8 char is invalid
                // go back and force it to parse as ISO or 1252
                $inside = false;
                $invalid = true;
                $offset = $originOffset - 1;
                continue;
            }

            // put this byte's data where it needs to go
            $ordcache |= ($ord & 0b00111111) << (6 * ($bytes - 1 - ($offset - $originOffset)));
            $cache .= $char;

            if ($originOffset + ($bytes - 1) === $offset) {
                // we're done parsing this char, now let's verify
                $inside = false;

                // check for overlong, surrogate, too large, BOM, or C0/C1
                $overlong = ($ordcache < self::$spec[$bytes]['start']);
                $surrogate = ($ordcache & 0xFFFFF800 === 0xD800);
                $toobig = ($ordcache > 0x10FFFF);

                if ($overlong || $surrogate || $toobig) {
                    $invalid = true;
                    $offset = $originOffset - 1;
                    continue;
                }

                if ($ordcache === 0xFEFF) { // BOM
                    if ($originOffset !== 0) {
                        // if not at beginning, store as word joiner U+2060
                        $this->chars[] = [\chr(0xE2) . \chr(0x81) . \chr(0xA0), 0x2060];
                    }
                    // otherwise discard
                    continue;
                }

                // verification passed, now store it
                $this->chars[] = [$cache, $ordcache];
            }
        }
    }
}
