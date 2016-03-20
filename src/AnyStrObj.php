<?php

namespace StringObject;

abstract class AnyStrObj
{
    protected $raw;

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

    abstract public function compareTo($str, $mode = self::NORMAL, $length = 1);

    abstract public function escape($mode = self::NORMAL, $charlist = '');

    abstract public function nextToken($delim);

    abstract public function remove($str, $mode = self::NORMAL);

    abstract public function repeat($times);

    /**
     * @param string $replace
     */
    abstract public function replace($search, $replace, $mode = self::NORMAL);

    abstract public function resetToken();

    abstract public function times($times);

    abstract public function translate($search, $replace = '');

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
        if (\is_object($thing) && !\method_exists($thing, '__toString')) {
            throw new \InvalidArgumentException(
                'Parameter is an object that does not implement __toString() method'
            );
        } elseif (\is_array($thing)) {
            throw new \InvalidArgumentException('Unsure of how to convert array to string');
        }
    }
}
