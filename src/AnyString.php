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

    /**
     * @return array
     */
    abstract public function toArray($delim = '', $limit = null);

    abstract public function compareTo($str, $mode = self::NORMAL, $length = 1);

    /**
     * @return AnyString
     */
    abstract public function escape($mode = self::NORMAL, $charlist = '');

    abstract public function nextToken($delim);

    abstract public function remove($str, $mode = self::NORMAL);

    abstract public function repeat($times);

    /**
     * @param string $replace
     */
    abstract public function replace($search, $replace, $mode = self::NORMAL);

    /**
     * @return AnyString
     */
    public function replaceWhole($replacement = '')
    {
        return new static($replacement);
    }

    abstract public function resetToken();

    abstract public function times($times);

    abstract public function translate($search, $replace = '');

    /**
     * @return AnyString
     */
    abstract public function trim($mask = " \t\n\r\0\x0B", $mode = self::BOTH_ENDS);

    abstract public function unescape($mode = self::NORMAL);

    abstract public function uuDecode();

    abstract public function uuEncode();

    public function equals($str)
    {
        self::testStringableObject($str);

        $str = (string) $str;
        return ($str == $this->raw);
    }

    abstract public function isAscii();

    abstract public function isEmpty();

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
