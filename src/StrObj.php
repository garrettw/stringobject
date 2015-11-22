<?php

namespace StringObject;

class StrObj implements \ArrayAccess, \Countable, \Iterator
{
    // CONSTANTS

    const NORMAL = 0;
    const START = 0;
    const END = 1;
    const BOTH_ENDS = 2;
    const CASE_INSENSITIVE = 4;
    const REVERSE = 8;
    const EXACT_POSITION = 16;
    const CURRENT_LOCALE = 32;
    const NATURAL_ORDER = 64;
    const FIRST_N = 128;
    const C_STYLE = 256;
    const META = 512;
    const LAZY = 1024;
    const GREEDY = 2048;
    const WINDOWS1252 = 4096;
    const UTF8 = 8192;

    // STATIC PROPERTIES

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
        '(c]' => ['©'],
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

    // PROPERTIES

    protected $raw;
    protected $encoding;
    protected $token = false;
    protected $caret = 0;

    // MAGIC METHODS

    public function __construct($thing, $enc = self::WINDOWS1252)
    {
        self::testStringableObject($thing);

        if (\is_array($thing)) {
            throw new \InvalidArgumentException('Unsure of how to convert array to string');
        }

        $this->raw = (string) $thing;
        $this->encoding = $enc;
    }

    /**
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->raw;
    }

    public function toArray($delim = '', $limit = null)
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

    public function charAt($offset)
    {
        return new self($this->raw{$offset});
    }

    public function charCodeAt($offset)
    {
        if ($this->encoding === self::WINDOWS1252) {
            return \ord($this->raw{$offset});
        }

        return $this->parseUtf8CharAt($offset);
    }

    public function compareTo($str, $mode = self::NORMAL, $length = 1)
    {
        $modemap = [
            self::NORMAL => 'strcmp',
            self::CASE_INSENSITIVE => 'strcasecmp',
            self::CURRENT_LOCALE => 'strcoll',
            self::NATURAL_ORDER => 'strnatcmp',
            (self::NATURAL_ORDER | self::CASE_INSENSITIVE) => 'strnatcasecmp',
            self::FIRST_N => 'strncmp',
            (self::FIRST_N | self::CASE_INSENSITIVE) => 'strncasecmp',
        ];

        if ($mode & self::FIRST_N) {
            return \call_user_func($modemap[$mode], $this->raw, $str, $length);
        }
        return \call_user_func($modemap[$mode], $this->raw, $str);
    }

    public function indexOf($needle, $offset = 0, $mode = self::NORMAL)
    {
        // strip out bits we don't understand
        $mode &= (self::REVERSE | self::CASE_INSENSITIVE);

        $modemap = [
            self::NORMAL => 'strpos',
            self::CASE_INSENSITIVE => 'stripos',
            self::REVERSE => 'strrpos',
            (self::REVERSE | self::CASE_INSENSITIVE) => 'strripos',
        ];
        return \call_user_func($modemap[$mode], $this->raw, $needle, $offset);
    }

    public function length()
    {
        return \strlen($this->raw);
    }

    // MODIFYING METHODS

    public function append($str)
    {
        return new self($this->raw . $str);
    }

    public function asciify($removeUnsupported = true)
    {
        $str = $this->raw;
        foreach (self::$asciimap as $key => $value) {
            $str = \str_replace($value, $key, $str);
        }
        if ($removeUnsupported) {
            $str = \preg_replace('/[^\x20-\x7E]/u', '', $str);
        }
        return new self($str);
    }

    public function chunk($length = 76, $ending = "\r\n")
    {
        return new self(\chunk_split($this->raw, $length, $ending));
    }

    public function concat($str)
    {
        return $this->append($str);
    }

    public function escape($mode = self::NORMAL, $charlist = '')
    {
        $modemap = [
            self::NORMAL => 'addslashes',
            self::C_STYLE => 'addcslashes',
            self::META => 'quotemeta',
        ];
        if ($mode === self::C_STYLE) {
            return new self(\call_user_func($modemap[$mode], $this->raw, $charlist));
        }
        return new self(\call_user_func($modemap[$mode], $this->raw));
    }

    public function insertAt($str, $offset)
    {
        return $this->replaceSubstr($str, $offset, 0);
    }

    public function nextToken($delim)
    {
        if ($this->token) {
            return new self(\strtok($delim));
        }
        $this->token = true;
        return new self(\strtok($this->raw, $delim));
    }

    public function pad($newlength, $padding = ' ', $mode = self::END)
    {
        return new self(\str_pad($this->raw, $newlength, $padding, $mode));
    }

    public function prepend($str)
    {
        return new self($str . $this->raw);
    }

    public function remove($str, $mode = self::NORMAL)
    {
        return $this->replace($str, '', $mode);
    }

    public function removeSubstr($start, $length = null)
    {
        return $this->replaceSubstr('', $start, $length);
    }

    public function repeat($times)
    {
        return new self(\str_repeat($this->raw, $times));
    }

    public function replace($search, $replace, $mode = self::NORMAL)
    {
        if ($mode & self::CASE_INSENSITIVE) {
            return new self(\str_ireplace($search, $replace, $this->raw));
        }
        return new self(\str_replace($search, $replace, $this->raw));
    }

    public function replaceSubstr($replacement, $start, $length = null)
    {
        if ($length === null) {
            $length = $this->length();
        }
        return new self(\substr_replace($this->raw, $replacement, $start, $length));
    }

    public function resetToken()
    {
        $this->token = false;
    }

    public function reverse()
    {
        return new self(\strrev($this->raw));
    }

    public function shuffle()
    {
        return new self(\str_shuffle($this->raw));
    }

    public function substr($start, $length = 'omitted')
    {
        if ($length === 'omitted') {
            return new self(\substr($this->raw, $start));
        }
        return new self(\substr($this->raw, $start, $length));
    }

    public function times($times)
    {
        return $this->repeat($times);
    }

    public function translate($search, $replace = '')
    {
        if (is_array($search)) {
            return new self(\strtr($this->raw, $search));
        }
        return new self(\strtr($this->raw, $search, $replace));
    }

    public function trim($mask = " \t\n\r\0\x0B", $mode = self::BOTH_ENDS)
    {
        $modemap = [
            self::START => 'ltrim',
            self::END => 'rtrim',
            self::BOTH_ENDS => 'trim',
        ];
        return new self(\call_user_func($modemap[$mode], $this->raw, $mask));
    }

    public function unescape($mode = self::NORMAL)
    {
        $modemap = [
            self::NORMAL => 'stripslashes',
            self::C_STYLE => 'stripcslashes',
            self::META => 'stripslashes',
        ];
        return new self(\call_user_func($modemap[$mode], $this->raw));
    }

    public function uuDecode()
    {
        return new self(\convert_uudecode($this->raw));
    }

    public function uuEncode()
    {
        return new self(\convert_uuencode($this->raw));
    }

    public function wordwrap($width = 75, $break = "\n")
    {
        return new self(\wordwrap($this->raw, $width, $break, false));
    }

    public function wordwrapBreaking($width = 75, $break = "\n")
    {
        return new self(\wordwrap($this->raw, $width, $break, true));
    }

    // TESTING METHODS

    public function contains($needle, $offset = 0, $mode = self::NORMAL)
    {
        if ($mode & self::EXACT_POSITION) {
            return ($this->indexOf($needle, $offset, $mode) === $offset);
        }
        return ($this->indexOf($needle, $offset, $mode) !== false);
    }

    public function countSubstr($needle, $offset = 0, $length = null)
    {
        if ($length === null) {
            return \substr_count($this->raw, $needle, $offset);
        }
        return \substr_count($this->raw, $needle, $offset, $length);
    }

    public function endsWith($str, $mode = self::NORMAL)
    {
        $mode &= self::CASE_INSENSITIVE;
        $offset = $this->length() - \strlen($str);
        return $this->contains($str, $offset, $mode | self::EXACT_POSITION | self::REVERSE);
    }

    public function equals($str)
    {
        self::testStringableObject($str);

        $str = (string) $str;
        return ($str == $this->raw);
    }

    public function isAscii()
    {
        $len = $this->length();

        for ($i = 0; $i < $len; $i++) {
            if ($this->charCodeAt($i) >= 128) {
                return false;
            }
        }
        return true;
    }

    public function isEmpty()
    {
        return empty($this->raw);
    }

    public function startsWith($str, $mode = self::NORMAL)
    {
        $mode &= self::CASE_INSENSITIVE;
        return $this->contains($str, 0, $mode | self::EXACT_POSITION);
    }

    // INTERFACE IMPLEMENTATION METHODS

    public function count()
    {
        return \strlen($this->raw);
    }

    public function current()
    {
        return $this->raw[$this->caret];
    }

    public function key()
    {
        return $this->caret;
    }

    public function next()
    {
        $this->caret++;
    }

    public function rewind()
    {
        $this->caret = 0;
    }

    public function valid()
    {
        return ($this->caret < \strlen($this->raw));
    }

    public function offsetExists($offset)
    {
        $offset = (int) $offset;
        return ($offset >= 0 && $offset < \strlen($this->raw));
    }

    public function offsetGet($offset)
    {
        return $this->raw{$offset};
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Cannot assign '.$value.' to immutable StrObj instance at index '.$offset);
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Cannot unset index '.$offset.' on immutable StrObj instance');
    }

    // PRIVATE STATIC FUNCTIONS

    protected static function testStringableObject($thing)
    {
        if (\is_object($thing) && !\method_exists($thing, '__toString')) {
            throw new \InvalidArgumentException(
                'Parameter is an object that does not implement __toString() method'
            );
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
            if ($length === 2 && $byte & 0b11000000) {
                // overlong ascii
                return [$start + 1, 1, ($offset === $start) ? \ord($this->raw{$start + 1}) : $byte];
            }
            return [$offset, 1, $current];
        }

        if ($valid === true) {

            if ($length === 2) {
                $bigcode = $byte & 0b00011111;
            }

            elseif ($length === 3) {
                $bigcode = $byte & 0b00001111;
            }

            elseif ($length === 4) {
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
                    if ($original < $offset + self::calcUtf8CharLength($prev)) {
                        return [$offset, $length, true, $byte];
                    }
                }
                return [$original, 1, false, $byte];
            }
            return [$original, 1, false, $byte];
        }

        if ($byte <= 0b11110100) {
            // valid UTF8 start byte, find the rest, determine if length is valid
            $actual = $length = self::calcUtf8CharLength($byte);

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

    protected static function calcUtf8CharLength($byte)
    {
        if (~$byte & 0b00001000) return 4;
        if (~$byte & 0b00010000) return 3;
        if (~$byte & 0b00100000) return 2;
        return 1;
    }
}
