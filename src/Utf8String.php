<?php

namespace StringObject;

use BadMethodCallException;

class Utf8String extends AbstractString
{
    const RAW = 0;
    const NFC = 1;
    const NFD = 2;
    const NFK = 4;
    const NFKC = 5;
    const NFKD = 6;

    protected $chars = [];
    protected $uhandler;
    protected $normform = self::RAW;

    protected static $spec = [
        2 => ['mask' => 0b00011111, 'start' => 0x80],
        3 => ['mask' => 0b00001111, 'start' => 0x800],
        4 => ['mask' => 0b00000111, 'start' => 0x10000],
        5 => ['mask' => 0b00000011, 'start' => 0x200000],
        6 => ['mask' => 0b00000001, 'start' => 0x4000000],
    ];

    protected static $asciimap = [
        'a' => ['à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ',
                'ầ', 'ẩ', 'ẫ', 'ậ', 'ā', 'ą', 'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ',
                'ἄ', 'ἅ', 'ἆ', 'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
                'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ'],
        'b' => ['б', 'β', 'Ъ', 'Ь', 'ب'],
        'c' => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
        'd' => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض'],
        'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ë', 'ē',
                'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ', 'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ',
                'έ', 'е', 'ё', 'э', 'є', 'ə'],
        'f' => ['ф', 'φ', 'ف'],
        'g' => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'],
        'h' => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'],
        'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į', 'ı', 'ι', 'ί',
                'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ', 'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ',
                'ῑ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'і', 'ї', 'и'],
        'j' => ['ĵ', 'ј', 'Ј'],
        'k' => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'],
        'l' => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'],
        'm' => ['м', 'μ', 'م'],
        'n' => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'],
        'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ',
                'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ',
                'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'و', 'θ'],
        'p' => ['п', 'π'],
        'r' => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'],
        's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'],
        't' => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'],
        'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'û', 'ū',
                'ů', 'ű', 'ŭ', 'ų', 'µ', 'у'],
        'v' => ['в'],
        'w' => ['ŵ', 'ω', 'ώ'],
        'x' => ['χ'],
        'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي'],
        'z' => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز'],
        'aa' => ['ع'],
        'ae' => ['ä', 'æ'],
        'ch' => ['ч'],
        'dj' => ['ђ', 'đ'],
        'dz' => ['џ'],
        'gh' => ['غ'],
        'kh' => ['х', 'خ'],
        'lj' => ['љ'],
        'nj' => ['њ'],
        'oe' => ['ö', 'œ'],
        'ps' => ['ψ'],
        'sh' => ['ш'],
        'shch' => ['щ'],
        'ss' => ['ß'],
        'th' => ['þ', 'ث', 'ذ', 'ظ'],
        'ts' => ['ц'],
        'ue' => ['ü'],
        'ya' => ['я'],
        'yu' => ['ю'],
        'zh' => ['ж'],
        '(c)' => ['©'],
        'A' => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ',
                'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ā', 'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ',
                'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
                'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А'],
        'B' => ['Б', 'Β'],
        'C' => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
        'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
        'E' => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ë', 'Ē',
                'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ', 'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ',
                'Ὲ', 'Е', 'Ё', 'Э', 'Є', 'Ə'],
        'F' => ['Ф', 'Φ'],
        'G' => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
        'H' => ['Η', 'Ή'],
        'I' => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į', 'İ', 'Ι', 'Ί',
                'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ', 'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И',
                'І', 'Ї'],
        'K' => ['К', 'Κ'],
        'L' => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'],
        'M' => ['М', 'Μ'],
        'N' => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
        'O' => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ',
                'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ø', 'Ō', 'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ',
                'Ὃ', 'Ὄ', 'Ὅ', 'Ὸ', 'Ό', 'О', 'Θ', 'Ө'],
        'P' => ['П', 'Π'],
        'R' => ['Ř', 'Ŕ', 'Р', 'Ρ'],
        'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
        'T' => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
        'U' => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Û', 'Ū',
                'Ů', 'Ű', 'Ŭ', 'Ų', 'У'],
        'V' => ['В'],
        'W' => ['Ω', 'Ώ'],
        'X' => ['Χ'],
        'Y' => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ'],
        'Z' => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
        'AE' => ['Ä', 'Æ'],
        'CH' => ['Ч'],
        'DJ' => ['Ђ'],
        'DZ' => ['Џ'],
        'KH' => ['Х'],
        'LJ' => ['Љ'],
        'NJ' => ['Њ'],
        'OE' => ['Ö'],
        'PS' => ['Ψ'],
        'SH' => ['Ш'],
        'SHCH' => ['Щ'],
        'SS' => ['ẞ'],
        'TH' => ['Þ'],
        'TS' => ['Ц'],
        'UE' => ['Ü'],
        'YA' => ['Я'],
        'YU' => ['Ю'],
        'ZH' => ['Ж'],
        ' ' => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82",
                "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86",
                "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
                "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80"],
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

    protected static $utf8ToC1u = [
        '€' => "\x80",
        '‚' => "\x82",
        'ƒ' => "\x83",
        '„' => "\x84",
        '…' => "\x85",
        '†' => "\x86",
        '‡' => "\x87",
        'ˆ' => "\x88",
        '‰' => "\x89",
        'Š' => "\x8A",
        '‹' => "\x8B",
        'Œ' => "\x8C",
        'Ž' => "\x8E",
        '‘' => "\x91",
        '’' => "\x92",
        '“' => "\x93",
        '”' => "\x94",
        '•' => "\x95",
        '–' => "\x96",
        '—' => "\x97",
        '˜' => "\x98",
        '™' => "\x99",
        'š' => "\x9A",
        '›' => "\x9B",
        'œ' => "\x9C",
        'ž' => "\x9E",
        'Ÿ' => "\x9F",
    ];

    public function toArray(mixed $delim = '', int $limit = null): array
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

    public function charAt(int $index): string
    {
        $this->parse();
        return $this->chars[$index][0];
    }

    public function charCodeAt(int $index): int
    {
        $this->parse();
        return $this->chars[$index][1];
    }

    public function chunk(int $length = 76, string $ending = "\r\n"): static
    {
        throw new BadMethodCallException('chunk() not implemented yet');
    }

    public function detectForm()
    {
    }

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

    public function current(): string
    {
        return $this->chars[$this->caret];
    }
    
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
        return $this->chars[$offset][0];
    }
    
    public function pad(int $length, string $padString = ' ', $padType = self::END): static
    {
        throw new BadMethodCallException('pad() not implemented yet');
    }

    public function substr(int $start, int $length = null): static
    {
        return new static(\implode('', \array_slice($this->chars, $start, $length)));
    }

    public function normalize(Normalize\Normalizer $norm)
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

    public function toAscii(bool $allow1252 = false)
    {
        $str = $this->__toString();
        // convert foreign text chars to ASCII equivalents
        foreach (static::$asciimap as $key => $value) {
            $str = \str_replace($value, $key, $str);
        }
        if ($allow1252) {
            // convert any UTF-8 chars that are also valid in Win-1252
            $str = \str_replace(array_keys(self::$utf8ToC1u), array_values(self::$utf8ToC1u), $str);
            
            // strip out anything left over
            return new AsciiString(\preg_replace('/[^\x00-\x9F]/u', '', $str));
        }

        // strip out any characters outside the ASCII text range
        return new AsciiString(\preg_replace('/[^\x00-\x7F]/u', '', $str));
    }

    public function replaceSubstr(string $replacement, int $start, ?int $length = null): static
    {
        throw new BadMethodCallException('replaceSubstr() is not implemented yet');
    }

    protected static function cpToUtf8Char(int $cpt): string
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

        $data = match (true) {
            ($cpt < self::$spec[3]['start']) => [
                0b11000000 | ($cpt >> 6),
                0b10000000 | ($cpt & 0b00111111)
            ],
            ($cpt < self::$spec[4]['start']) => [
                0b11100000 | ($cpt >> 12),
                0b10000000 | (($cpt >> 6) & 0b00111111),
                0b10000000 | ($cpt & 0b00111111),
            ],
            default => [
                0b11110100,
                0b10000000 | (($cpt >> 12) & 0b00111111),
                0b10000000 | (($cpt >> 6) & 0b00111111),
                0b10000000 | ($cpt & 0b00111111),
            ]
        };

        return implode(array_map('chr', $data));
    }
    
    protected static function charLength(int $byte)
    {
        return match (true) {
            ($byte < 192) => 1,
            (($byte & 0b11100000) === 0b11000000) => 2,
            (($byte & 0b11110000) === 0b11100000) => 3,
            (($byte & 0b11111000) === 0b11110000) => 4,
            (($byte & 0b11111100) === 0b11111000) => 5,
            (($byte & 0b11111110) === 0b11111100) => 6,
            default => false
        };
    }

    private function parse()
    {
        if (!empty($this->chars)) {
            // it's already been parsed
            return;
        }

        $len = \strlen($this->raw);
        $inside = false; // are we "inside" of evaluating a valid UTF-8 char?
        $invalid = false;
        $originOffset = $bytes = $ordcache = 0;
        $cache = '';

        for ($offset = 0; $offset < $len; $offset++) {
            $char = $this->raw[$offset];
            $ord = \ord($char);

            if ($inside === false) {
                $bytes = self::charLength($ord);

                if ($bytes > 1 && $offset + $bytes <= $len && $invalid === false) {
                    // valid UTF-8 multibyte start
                    $inside = true;
                    $cache = $char;
                    $ordcache = ($ord & self::$spec[$bytes]['mask']) << (6 * ($bytes - 1));
                    $originOffset = $offset;
                    continue;
                }
                if ($ord < self::$spec[2]['start']) {
                    // ASCII 7-bit char
                    $this->chars[] = [$char, $ord];
                    continue;
                }

                // could be C0/C1 block; if so, map from cp1252 to utf8
                if (isset(self::$winc1umap[$ord])) {
                    $ord = self::$winc1umap[$ord];
                }
                // do the conversion and store the char
                $this->chars[] = [self::cpToUtf8Char($ord), $ord];
                $invalid = false;
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
                $surrogate = (($ordcache & 0xFFFFF800) === 0xD800);
                $toobig = ($ordcache > 0x10FFFF);

                if ($overlong || $surrogate || $toobig) {
                    $invalid = true;
                    $offset = $originOffset - 1;
                    continue;
                }

                if ($ordcache === 0xFEFF) { // BOM
                    if ($originOffset !== 0) {
                        // if not at beginning, store as word joiner U+2060
                        $this->chars[] = ["\xE2\x81\xA0", 0x2060];
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
