<?php

namespace StringObject;

class UStrObj extends StrObj
{
    protected $chars = [];
    protected $uhandler;

    protected static $spec = [
        2 => ['datamask' => 0b00011111, 'threshold' => 0x80],
        3 => ['datamask' => 0b00001111, 'threshold' => 0x800],
        4 => ['datamask' => 0b00000111, 'threshold' => 0x10000],
        5 => ['datamask' => 0b00000011, 'threshold' => 0x200000],
        6 => ['datamask' => 0b00000001, 'threshold' => 0x4000000],
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

    public function __construct($thing)
    {
        parent::__construct($thing);
    }

    public function toArray($delim = '', $limit = null)
    {
        $this->loadToArray();

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

    public function charAt($index)
    {
        $this->loadToArray();
        return $this->chars[$index][0];
    }

    public function charCodeAt($index)
    {
        $this->loadToArray();
        return $this->chars[$index][1];
    }

    private function loadToArray()
    {
        if (!empty($this->chars)) {
            return;
        }

        $len = \strlen($this->raw);
        $inside = false;
        $invalid = false;
        $cache = '';
        $ordcache = 0;
        $originOffset = 0;
        $bytes = 0;

        for ($offset = 0; $offset < $len; $offset++) {
            $char = $this->raw{$offset};
            $ord = \ord($char);

            if ($inside === false) {
                $bytes = self::charLength($ord);

                if ($bytes > 1 && $offset + $bytes <= $len && $invalid === false) {
                    // valid UTF-8 multibyte start
                    $inside = true;
                    $cache = $char;
                    $ordcache = ($ord & self::$spec[$bytes]['datamask']) << (6 * ($bytes - 1));
                    $originOffset = $offset;
                } elseif ($ord < 0x80) {
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
                $overlong = ($ordcache < self::$spec[$bytes]['threshold']);
                $surrogate = ($ordcache & 0xFFFFF800 === 0xD800);
                $toobig = ($ordcache > 0x10FFFF);

                if ($overlong || $surrogate || $toobig) {
                    $inside = false;
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
                // then clear out the temp vars for the next sequence
                $inside = false;
                $invalid = false;
                $cache = '';
                $ordcache = 0;
            }
        }
    }

    /**
     *
     */
    protected static function cpToUtf8Char($cpt)
    {
        if ($cpt < 0x80) {
            return \chr($cpt);
        }

        $data = [];
        if ($cpt < 0x800) {
            $data = [
                0b11000000 | ($cpt >> 6),
                0b10000000 | ($cpt & 0b00111111)
            ];
        } elseif ($cpt < 0x10000) {
            $data = [
                0b11100000 | ($cpt >> 12),
                0b10000000 | (($cpt >> 6) & 0b00111111),
                0b10000000 | ($cpt & 0b00111111),
            ];
        } elseif ($cpt < 0x10FFFF) {
            $data = [
                0b11110100,
                0b10000000 | (($cpt >> 12) & 0b00111111),
                0b10000000 | (($cpt >> 6) & 0b00111111),
                0b10000000 | ($cpt & 0b00111111),
            ];
        } else {
            $data = [0xEF, 0xBF, 0xBD]; // U+FFFD
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
}
