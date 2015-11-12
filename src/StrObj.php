<?php

namespace StringObject;

class StrObj implements \ArrayAccess, \Countable, \Iterator
{
    const CASE_SENSITIVE = 0;
    const CASE_INSENSITIVE = 1;

    private $raw;
    private $token = false;
    private $caret = 0;
    private static $stdFuncs = [
        'addcslashes', 'addslashes', 'bin2hex', 'chop', 'chunk_split',
        'convert_cyr_string', 'convert_uudecode', 'convert_uuencode', 'count_chars',
        'crc32', 'crypt', 'hebrev', 'hebrevc', 'hex2bin', 'html_entity_decode',
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
        'countChars' => 'count_chars',
        'htmlEntityDecode' => 'html_entity_decode',
        'htmlEntityEncode' => 'htmlentities',
        'htmlSpecialCharsDecode' => 'htmlspecialchars_decode',
        'htmlSpecialCharsEncode' => 'htmlspecialchars',
        'firstCharToLowerCase' => 'lcfirst',
        'toCharCode' => 'ord',
        'quotedPrintableDecode' => 'quoted_printable_decode',
        'quotedPrintableEncode' => 'quoted_printable_encode',
        'similarText' => 'similar_text',
        'scanf' => 'sscanf',
        'getCSV' => 'str_getcsv',
        'pad' => 'str_pad',
        'repeat' => 'str_repeat',
        'times' => 'str_repeat',
        'rot13' => 'str_rot13',
        'shuffle' => 'str_shuffle',
        'split' => 'str_split',
        'toArray' => 'str_split',
        'countWords' => 'str_word_count',
        'icompare' => 'strcasecmp',
        'substrFromCharToEnd' => 'strchr',
        'compare' => 'strcmp',
        'compareLocale' => 'strcoll',
        'lengthBeforeCharMask' => 'strcspn',
        'stripTags' => 'strip_tags',
        'iindexOf' => 'stripos',
        'isubstrFromCharToEnd' => 'stristr',
        'isubstrFromStringToEnd' => 'stristr',
        'length' => 'strlen',
        'icompareNatural' => 'strnatcasecmp',
        'compareNatural' => 'strnatcmp',
        'icompareFirstN' => 'strncasecmp',
        'compareFirstN' => 'strncmp',
        'substrFromCharListToEnd' => 'strpbrk',
        'indexOf' => 'strpos',
        'substrFromLastCharToEnd' => 'strrchr',
        'reverse' => 'strrev',
        'iindexOfLast' => 'strripos',
        'indexOfLast' => 'strrpos',
        'lengthOfMasked' => 'strspn',
        'substrFromStringToEnd' => 'strstr',
        'toLowerCase' => 'strtolower',
        'tokenize' => 'strtok',
        'nextToken' => 'strtok',
        'toUpperCase' => 'strtoupper',
        'translate' => 'strtr',
        'compareSubstr' => 'substr_compare',
        'countSubstr' => 'substr_count',
        'replace' => 'substr_replace',
        'firstCharToUpperCase' => 'ucfirst',
        'wordsToUpperCase' => 'ucwords',
    ];

    public function __construct($raw)
    {
        if (is_object($raw)) $raw = $raw->__toString();
        if (is_array($raw)) $raw = implode($raw);

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
        if (\array_key_exists($method, self::$apiMap)) {
            $result = \call_user_func_array([$this, self::$apiMap[$method]], $args);
            return $this->getSelfIfString($result);
        }

        if (!\in_array($method, self::$stdFuncs)) {
            throw new \BadMethodCallException("Method $method does not exist");
        }

        \array_unshift($args, $this->raw);
        return $this->getSelfIfString(\call_user_func_array($method, $args));
    }

    public static function make($str)
    {
        return new self($str);
    }

    public function charAt($i)
    {
        return $this->raw{$i};
    }

    public function charCodeAt($i)
    {
        return \ord($this->raw{$i});
    }

    public function isEmpty()
    {
        return empty($this->raw);
    }

    public function explode()
    {
        return $this->callWithAltArgPos('explode', \func_get_args(), 1);
    }

    public function str_ireplace()
    {
        return new self($this->callWithAltArgPos('str_ireplace', \func_get_args(), 2));
    }

    public function str_replace()
    {
        return new self($this->callWithAltArgPos('str_replace', \func_get_args(), 2));
    }

    public function strtok($delim)
    {
        if ($this->token) {
            return new self(\strtok($delim));
        }
        $this->token = true;
        return new self(\strtok($this->raw, $delim));
    }

    public function resetToken()
    {
        $this->token = false;
    }

    public function append($str)
    {
        return new self($this->raw . $str);
    }

    public function prepend($str)
    {
        return new self($str . $this->raw);
    }

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
        throw new \LogicException('Invalid assignment operation on immutable StrObj instance');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Invalid unset operation on immutable StrObj instance');
    }

    private function callWithAltArgPos($func, $args, $pos)
    {
        \array_splice($args, $pos, 0, $this->raw);
        return \call_user_func_array($func, $args);
    }

    private function getSelfIfString($val)
    {
        if (\is_string($val)) {
            return new self($val);
        }
        return $val;
    }
}
