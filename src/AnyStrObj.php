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

    public abstract function compareTo($str, $mode = self::NORMAL, $length = 1);

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

    public abstract function escape($mode = self::NORMAL, $charlist = '');

    public abstract function nextToken($delim);

    public abstract function remove($str, $mode = self::NORMAL);

    public abstract function repeat($times);

    /**
     * @param string $replace
     */
    public abstract function replace($search, $replace, $mode = self::NORMAL);

    public abstract function resetToken();

    public abstract function times($times);

    public abstract function translate($search, $replace = '');

    public abstract function trim($mask = " \t\n\r\0\x0B", $mode = self::BOTH_ENDS);

    public abstract function unescape($mode = self::NORMAL);

    public abstract function uuDecode();

    public abstract function uuEncode();

    public function equals($str)
    {
        self::testStringableObject($str);

        $str = (string) $str;
        return ($str == $this->raw);
    }

    public abstract function isAscii();

    public abstract function isEmpty();

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
