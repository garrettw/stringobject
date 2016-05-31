<?php

namespace StringObject;

abstract class AnyString implements \ArrayAccess, \Countable, \Iterator
{
    // CONSTANTS

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

    // PROPERTIES

    protected $raw;
    protected $caret = 0;
    protected $token = false;

    /**
     * @param mixed $thing Anything that can be cast to a string
     */
    public function __construct($thing)
    {
        self::testStringableObject($thing);
        $this->raw = (string) $thing;
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

    // ArrayAccess methods {

    public function offsetExists($offset)
    {
        $offset = (int) $offset;
        return ($offset >= 0 && $offset < $this->count());
    }

    // END ArrayAccess methods }

    // Iterator methods {

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
        return ($this->caret < $this->count());
    }

    // END Iterator methods }

    public function equals($str)
    {
        self::testStringableObject($str);

        $str = (string) $str;
        return ($str == $this->raw);
    }

    /**
     * @return bool
     */
    abstract public function isAscii();

    /**
     * @return bool
     */
    abstract public function isEmpty();

    /**
     * @return array
     */
    abstract public function toArray($delim = '', $limit = null);

    /**
     * @return AnyString
     */
    abstract public function append($str);

    /**
     * @return int
     */
    abstract public function compareTo($str, $mode = self::NORMAL, $length = 1);

    /**
     * @return AnyString
     */
    public function concat($str)
    {
        return $this->append($str);
    }

    /**
     * @return AnyString
     */
    abstract public function escape($mode = self::NORMAL, $charlist = '');

    public function hexDecode()
    {
        return $this->replaceWhole(\hex2bin($this->raw));
    }

    public function hexEncode()
    {
        return $this->replaceWhole(\bin2hex($this->raw));
    }

    public function nextToken($delim)
    {
        if ($this->token) {
            return new static(\strtok($delim));
        }
        $this->token = true;
        return new static(\strtok($this->raw, $delim));
    }

    /**
     * @return AnyString
     */
    abstract public function prepend($str);

    /**
     * @return AnyString
     */
    abstract public function remove($str, $mode = self::NORMAL);

    /**
     * @return AnyString
     */
    abstract public function removeSubstr($start, $length = null);

    /**
     * @return AnyString
     */
    abstract public function repeat($times);

    /**
     * @param string $replace
     *
     * @return AnyString
     */
    abstract public function replace($search, $replace, $mode = self::NORMAL);

    /**
     * @return AnyString
     */
    abstract public function replaceSubstr($replacement, $start, $length = null);

    /**
     * @return AnyString
     */
    public function replaceWhole($replacement = '')
    {
        return new static($replacement);
    }

    public function resetToken()
    {
        $this->token = false;
    }

    /**
     * @return AnyString
     */
    abstract public function substr($start, $length = 'omitted');

    /**
     * @return AnyString
     */
    public function times($times)
    {
        return $this->repeat($times);
    }

    /**
     * @return AnyString
     */
    abstract public function translate($search, $replace = '');

    /**
     * @return AnyString
     */
    abstract public function trim($mask = " \t\n\r\0\x0B", $mode = self::BOTH_ENDS);

    /**
     * @return AnyString
     */
    abstract public function unescape($mode = self::NORMAL);

    public function uuDecode()
    {
        return $this->replaceWhole(\convert_uudecode($this->raw));
    }

    public function uuEncode()
    {
        return $this->replaceWhole(\convert_uuencode($this->raw));
    }

    protected static function testStringableObject($thing)
    {
        if (is_string($thing)) {
            return true;
        }

        if (\is_object($thing) && !\method_exists($thing, '__toString')) {
            throw new \InvalidArgumentException(
                'Parameter is an object that does not implement __toString() method'
            );
        } elseif (\is_array($thing)) {
            throw new \InvalidArgumentException('Unsure of how to convert array to string');
        }
    }
}
