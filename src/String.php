<?php

namespace StrObj;

class String
{
    private $raw;
    private $token = false;
    private static $stdFuncs = [
        'addcslashes', 'addslashes', 'bin2hex', 'chop', 'chunk_split',
        'convert_cyr_string', 'convert_uudecode', 'convert_uuencode', 'crc32',
        'crypt', 'empty', 'hebrev', 'hebrevc', 'hex2bin', 'html_entity_decode',
        'htmlentities', 'htmlspecialchars_decode', 'htmlspecialchars',
        'lcfirst', 'levenshtein', 'ltrim', 'md5', 'metaphone', 'nl2br', 'ord',
        'quoted_printable_decode', 'quoted_printable_encode', 'quotemeta',
        'rtrim', 'sha1', 'similar_text', 'soundex', 'sscanf', 'str_getcsv',
        'str_pad', 'str_repeat', 'str_rot13', 'str_shuffle', 'str_split',
        'str_word_count', 'strcasecmp', 'strchr', 'strcmp', 'strcoll',
        'strcspn', 'strip_tags', 'stripcslashes', 'stripos', 'stripslashes',
        'stristr', 'strlen', 'strnatcasecmp', 'strnatcmp', 'strncasecmp',
        'strncmp', 'strpbrk', 'strpos', 'strrchr', 'strrev', 'strripos',
        'strrpos', 'strspn', 'strstr', 'strtolower', 'strtoupper', 'strtr',
        'substr_compare', 'substr_count', 'substr_replace', 'substr', 'trim',
        'ucfirst', 'ucwords', 'wordwrap',
    ];
    private static $apiMap = [ // 'new' => 'old'
        'chunkSplit' => 'chunk_split',
        'convertCyrillic' => 'convert_cyr_string',
        'uudecode' => 'convert_uudecode',
        'uuencode' => 'convert_uuencode',
        'htmlEntityDecode' => 'html_entity_decode',
        'htmlEntityEncode' => 'htmlentities',
        'htmlSpecialCharsDecode' => 'htmlspecialchars_decode',
        'htmlSpecialCharsEncode' => 'htmlspecialchars',
        'quotedPrintableDecode' => 'quoted_printable_decode',
        'quotedPrintableEncode' => 'quoted_printable_encode',
        'similarText' => 'similar_text',
        'scanf' => 'sscanf',
        'getCSV' => 'str_getcsv',
        'pad' => 'str_pad',
        'repeat' => 'str_repeat',
        'rot13' => 'str_rot13',
        'shuffle' => 'str_shuffle',
        'split' => 'str_split',
        'countWords' => 'str_word_count',
        'icompare' => 'strcasecmp',
        'compare' => 'strcmp',
        'compareLocale' => 'strcoll',
        'lengthBeforeMask' => 'strcspn',
        'stripTags' => 'strip_tags',
        'iindexOf' => 'stripos',
        'isubStartFromString' => 'stristr',
        'length' => 'strlen',
        'icompareNatural' => 'strnatcasecmp',
        'compareNatural' => 'strnatcmp',
        'icompareFirstN' => 'strncasecmp',
        'compareFirstN' => 'strncmp',
        'substrStartFromCharList' => 'strpbrk',
        'indexOf' => 'strpos',
        'substrStartFromLastChar' => 'strrchr',
        'reverse' => 'strrev',
        'iindexOfLast' => 'strripos',
        'indexOfLast' => 'strrpos',
        'lengthOfMasked' => 'strspn',
        'subStartFromString' => 'strstr',
        'token' => 'strtok',
        'toLower' => 'strtolower',
        'nextToken' => 'strtok',
        'toUpper' => 'strtoupper',
        'translate' => 'strtr',
        'compareSubs' => 'substr_compare',
        'countSubs' => 'substr_count',
        'replace' => 'substr_replace',
    ];

    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    public function __toString()
    {
        return $this->raw;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __call($method, $args)
    {
        if (array_key_exists($method, self::$apiMap)) {
            return call_user_func_array(array($this, self::$apiMap[$method]), $args);
        }

        if (!in_array($method, self::$stdFuncs)) {
            return;
        }

        array_unshift($args, $this->raw);
        $val = call_user_func_array($method, $args);

        if (is_string($val)) {
            return new self($val);
        }
        return $val;
    }

    public function __invoke($str)
    {
        return new self($str);
    }

    public function count_chars($mode = 0)
    {
        $result = call_user_func('count_chars', $this->raw, $mode);

        if ($mode >= 3) {
            return new self($result);
        }

        return $result;
    }

    public function explode()
    {
        return $this->callWithAltArgPos('explode', func_get_args(), 1);
    }

    public function str_ireplace()
    {
        return new self($this->callWithAltArgPos('str_ireplace', func_get_args(), 2));
    }

    public function str_replace()
    {
        return new self($this->callWithAltArgPos('str_replace', func_get_args(), 2));
    }

    public function strtok($delim)
    {
        if ($this->token) {
            return new self(strtok($delim));
        }
        return $this->tokenize($delim);
    }

    public function tokenize($delim)
    {
        $this->token = true;
        return new self(strtok($this->raw, $delim));
    }

    public function resetToken()
    {
        $this->token = false;
    }

    private function callWithAltArgPos($func, $args, $pos)
    {
        return call_user_func_array($func, array_splice($args, $pos, 0, $this->raw));
    }
}
