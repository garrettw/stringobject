<?php

namespace StringObject;

use BadMethodCallException;
use StringObject\Utf8\Parser;

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

    /** @var array<string, string[]> */
    protected static array $asciimap = [
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

    /** @var array<string, string> */
    protected static array $utf8ToC1u = [
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

    public function toAscii(bool $allow1252 = false): AsciiString
    {
        $str = $this->__toString();

        if (\extension_loaded('intl') && \function_exists('transliterator_transliterate')) {
            $transliterated = \transliterator_transliterate('Any-Latin; Latin-ASCII; [^\\p{ASCII}] Remove', $str);
            if ($transliterated !== false) {
                return new AsciiString($transliterated);
            }
        }

        // convert foreign text chars to ASCII equivalents
        foreach (static::$asciimap as $key => $value) {
            $str = \str_replace($value, $key, $str);
        }
        if ($allow1252) {
            // convert any UTF-8 chars that are also valid in Win-1252
            $str = \str_replace(array_keys(self::$utf8ToC1u), array_values(self::$utf8ToC1u), $str);
            
            // strip out anything left over
            return new AsciiString(\preg_replace('/[^\x00-\x9F]/u', '', $str) ?: '');
        }

        // strip out any characters outside the ASCII text range
        return new AsciiString(\preg_replace('/[^\x00-\x7F]/u', '', $str) ?: '');
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
