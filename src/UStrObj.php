<?php

namespace StringObject;

class UStrObj extends StrObj
{
    protected $chars = [];
    protected $uhandler;

    protected static $masks = [
        2 => 0b00011111,
        3 => 0b00001111,
        4 => 0b00000111,
    ];
    protected static $winc1umap = [
        128 => 0x20AC,
        130 => 0x201A,
        131 => 0x0192,
        132 => 0x201E,
        133 => 0x2026,
        134 => 0x2020,
        135 => 0x2021,
        136 => 0x02C6,
        137 => 0x2030,
        138 => 0x0160,
        139 => 0x2039,
        140 => 0x0152,
        142 => 0x017D,
        145 => 0x2018,
        146 => 0x2019,
        147 => 0x201C,
        148 => 0x201D,
        149 => 0x2022,
        150 => 0x2013,
        151 => 0x2014,
        152 => 0x02DC,
        153 => 0x2122,
        154 => 0x0161,
        155 => 0x203A,
        156 => 0x0153,
        158 => 0x017E,
        159 => 0x0178,
    ];

    protected static $c1umap = [
        0xC280 => 0x20AC,
        0xC282 => 0x201A,
        0xC283 => 0x0192,
        0xC284 => 0x201E,
        0xC285 => 0x2026,
        0xC286 => 0x2020,
        0xC287 => 0x2021,
        0xC288 => 0x02C6,
        0xC289 => 0x2030,
        0xC28A => 0x0160,
        0xC28B => 0x2039,
        0xC28C => 0x0152,
        0xC28E => 0x017D,
        0xC291 => 0x2018,
        0xC292 => 0x2019,
        0xC293 => 0x201C,
        0xC294 => 0x201D,
        0xC295 => 0x2022,
        0xC296 => 0x2013,
        0xC297 => 0x2014,
        0xC298 => 0x02DC,
        0xC299 => 0x2122,
        0xC29A => 0x0161,
        0xC29B => 0x203A,
        0xC29C => 0x0153,
        0xC29E => 0x017E,
        0xC29F => 0x0178,
    ];

    public function __construct($thing, $uhandler)
    {
        parent::__construct($thing);
        $this->uhandler = $uhandler;
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
        return $this->chars[$index];
    }

    public function charCodeAt($index)
    {
        $this->loadToArray();
        $bytes = \array_map('ord', \str_split($this->chars[$index]));
        $count = \strlen($this->chars[$index]);

        if ($count === 1) {
            if ($bytes[0] > 0b01111111 && $bytes[0] < 0b10100000) {
                return self::$winc1umap[$bytes[0]];
            }
            return $bytes[0];
        }

        $overlong = false;

        foreach ($bytes as $i => $data) {
            if ($i === 0) {
                $codepoint = ($data & self::$masks[$count]);
                $overlong = ($codepoint === 0);
                continue;
            }
            $codepoint <<= 6;
            $codepoint += $data & 0b00111111;
        }

        if ($codepoint > 0x10FFFF) {
            // invalid
        }

        return $codepoint;
    }

    private function loadToArray()
    {
        if (!empty($this->chars)) {
            return;
        }

        $offset = 0;
        $len = \strlen($this->raw);
        while ($offset < $len) {
            $data = $this->raw{$offset};
            $bytes = self::charLength($data);
            $valid = ($offset + $bytes <= $len);

            for ($pos = 2; $pos <= $bytes && $valid === true; $pos++) {
                $byte = $this->raw{$offset + $pos - 1};
                $ord = \ord($byte);

                if ($ord < 128 && $ord > 191) {
                    $valid = false;
                }
                $data .= $byte;
            }

            if ($bytes === 1 || $valid === false) {
                $this->chars[] = $this->raw{$offset++};
                continue;
            }

            $this->chars[] = $data;
            $offset += $bytes;
        }
    }

    protected function parseUtf8CharAt($offset)
    {
        list($start, $length, $valid, $current) = $this->findUtf8CharAt($offset);

        if ($length === 1) {
            if ($current > 0b01111111 && $current < 0b10100000) {
                return [$start, $length, self::$winc1umap[$current]];
            }
            return [$start, $length, $current];
        }

        $byte = \ord($this->raw{$start});

        if ($valid === false) {
            if ($length === 2 && $byte === 0b11000000) {
                // overlong ascii
                return [$start + 1, 1, ($offset === $start) ? \ord($this->raw{$start + 1}) : $byte];
            }
            return [$offset, 1, $current];
        }

        if ($valid === true) {
            $bigcode = $byte & 0b00011111;

            if ($length === 3) {
                $bigcode = $byte & 0b00001111;
            } elseif ($length === 4) {
                $bigcode = $byte & 0b00000111;
            }

            for ($next = 1; $next < $length; $next++) {
                $bigcode <<= 6;
                $bigcode += \ord($this->raw{$start + $next}) & 0b00111111;
            }

            if ($bigcode > 0x10FFFF) {
                return [$offset, 1, $current];
            }
            return [$start, $length, $bigcode];
        }
    }

    /**
     * Determines if the byte at the given offset is part of a valid UTF8 char,
     * and returns its actual starting offset, length in bytes, validity,
     * and the byte at the original offset.
     */
    protected function findUtf8CharAt($offset)
    {
        $byte = \ord($this->raw{$offset});

        if ($byte <= 0b01111111) {
            // ASCII passthru, 1 byte long
            return [$offset, 1, true, $byte];
        }

        if ($byte <= 0b10111111) {
            // either part of a UTF8 char, or an invalid UTF8 codepoint.
            // try to find start of UTF8 char
            $original = $offset;
            while ($offset > 0 && $original - $offset < 4) {
                $prev = \ord($this->raw{--$offset});

                if ($prev <= 0b01111111) {
                    // prev is plain ASCII so current char can't be valid
                    return [$original, 1, false, $byte];
                }

                if ($prev <= 0b10111111) {
                    // prev is also part of a UTF8 char, so keep looking
                    continue;
                }

                if ($prev == 0xC0 || $prev == 0xC1) {
                    // prev is an invalid UTF8 starter for overlong ASCII
                    return [$offset, 2, false, $byte];
                }

                if ($prev <= 0b11110100) {
                    // prev is valid start byte, validate length to check this char
                    $length = self::charLength($prev);

                    if ($original < $offset + $length) {
                        return [$offset, $length, true, $byte];
                    }
                }
                return [$original, 1, false, $byte];
            }
            return [$original, 1, false, $byte];
        }

        if ($byte <= 0b11110100) {
            // valid UTF8 start byte, find the rest, determine if length is valid
            $actual = $length = self::charLength($byte);

            for ($i = 1; $i < $length; $i++) {
                if ($offset + $i >= $this->length()) {
                    $actual = $i - 1;
                    break;
                }
                $last = \ord($this->raw{$offset + $i});
                if ($last < 0b10000000 || $last > 0b10111111) {
                    $actual = $i;
                    break;
                }
            }

            if ($actual !== $length) {
                return [$offset, $actual, false, $byte];
            }
            return [$offset, $length, true, $byte];
        }

        // if 245 to 255, Windows-1252 passthru
        return [$offset, 1, false, $byte];
    }

    /**
     * @param integer $byte
     */
    protected static function charLength($byte)
    {
        if ($byte >> 3 === 0b00011110) {
            return 4;
        }
        if ($byte >> 4 === 0b00001110) {
            return 3;
        }
        if ($byte >> 5 === 0b00000110) {
            return 2;
        }
        return 1;
    }
}
