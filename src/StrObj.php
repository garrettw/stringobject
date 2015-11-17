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
    const AT_POSITION = 16;

    // PROPERTIES

    private $raw;
    private $token = false;
    private $caret = 0;

    // STATIC FUNCTIONS

    public static function make($str)
    {
        return new self($str);
    }

    // MAGIC METHODS

    public function __construct($thing)
    {
        self::stringableOrDie($thing);

        if (is_array($thing)) {
            $thing = \implode($thing);
        }

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

    public function toArray($delim = '', $limit = false)
    {
        if (empty($delim)) {
            return \str_split($this->raw);
        }
        if ($limit === false) {
            return \explode($delim, $this->raw);
        }
        return \explode($delim, $this->raw, $limit);
    }

    // INFORMATIONAL METHODS

    public function charAt($offset)
    {
        return new self($this->raw{$offset});
    }

    public function charCodeAt($offset, $utf8 = true)
    {
        $code = \ord($this->raw{$offset});

        if ($utf8 === true && $code > 191 && $code < 248) {
            $extrabytes = 1;
            $bigcode = $code & 31;

            if ($code > 223) {
                $extrabytes++;
                $bigcode &= 15;
            }

            if ($code > 239) {
                $extrabytes++;
                $bigcode &= 7;
            }

            if ($offset + $extrabytes >= $this->length()) {
                // in case the string is too short to have all the indicated bytes
                return $code;
            }

            for ($next = 1; $next <= $extrabytes; $next++) {
                $bigcode <<= 6;
                $bigcode += \ord($this->raw{$offset + $next}) & 63;
            }
            $code = $bigcode;
        }

        return $code;
    }

    public function indexOf($needle, $offset = 0, $mode = self::NORMAL)
    {
        switch ($mode) {
            case self::NORMAL:
                $funcname = 'strpos';
                break;

            case self::CASE_INSENSITIVE:
                $funcname = 'stripos';
                break;

            case self::REVERSE:
                $funcname = 'strrpos';
                break;

            case (self::REVERSE & self::CASE_INSENSITIVE):
                $funcname = 'strripos';
                break;

            default:
                throw new \InvalidArgumentException('Parameter 3 (mode) is invalid');
        }
        return \call_user_func($funcname, $this->raw, $needle, $offset);
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

    public function concat($str)
    {
        return new self($this->raw . $str);
    }

    public function nextToken($delim)
    {
        if ($this->token) {
            return new self(\strtok($delim));
        }
        $this->token = true;
        return new self(\strtok($this->raw, $delim));
    }

    public function pad($length, $padding = ' ', $mode = self::END)
    {
        return new self(\str_pad($this->raw, $length, $padding, $mode));
    }

    public function prepend($str)
    {
        return new self($str . $this->raw);
    }

    public function repeat($x)
    {
        return new self(\str_repeat($this->raw, $x));
    }

    public function replace($search, $replace, $mode = self::NORMAL)
    {
        if ($mode & self::CASE_INSENSITIVE) {
            return new self(\str_ireplace($search, $replace, $this->raw));
        }
        return new self(\str_replace($search, $replace, $this->raw));
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

    public function translate($from, $to = '')
    {
        if (is_array($from)) {
            return new self(\strtr($this->raw, $from));
        }
        return new self(\strtr($this->raw, $from, $to));
    }

    public function trim($mask = " \t\n\r\0\x0B", $mode = self::BOTH_ENDS)
    {
        switch ($mode) {
            case self::NORMAL:
            case self::BOTH_ENDS:
                $funcname = 'trim';
                break;

            case self::START:
                $funcname = 'ltrim';
                break;

            case self::END:
                $funcname = 'rtrim';
                break;

            default:
                throw new \InvalidArgumentException('Parameter 1 (mode) is invalid');
        }
        return new self(\call_user_func($funcname, $this->raw, $mask));
    }

    public function wordwrap($width = 75, $break = "\n", $cut = false)
    {
        return new self(\wordwrap($this->raw, $width, $break, $cut));
    }

    // TESTING METHODS

    public function contains($needle, $offset = 0, $mode = self::NORMAL)
    {
        if ($mode & self::AT_POSITION) {
            return ($this->indexOf($needle, $offset, $mode) === $offset);
        }
        return ($this->indexOf($needle, $offset, $mode) !== false);
    }

    public function equals($str)
    {
        self::stringableOrDie($str);

        $str = (string) $str;
        return ($str == $this->raw);
    }

    public function isEmpty()
    {
        return empty($this->raw);
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
        throw new \LogicException('Invalid assignment operation on immutable StrObj instance');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Invalid unset operation on immutable StrObj instance');
    }

    // PRIVATE METHODS

    /**
     * @return string|StrObj
     */
    private function callWithAltArgPos($func, $args, $pos)
    {
        \array_splice($args, $pos, 0, $this->raw);
        return \call_user_func_array($func, $args);
    }

    // PRIVATE STATIC FUNCTIONS

    private static function newSelfIfString($val)
    {
        if (\is_string($val)) {
            return new self($val);
        }
        return $val;
    }

    private static function stringableOrDie($thing)
    {
        if (\is_object($thing) && !\method_exists($thing, '__toString')) {
            throw new \InvalidArgumentException(
                'Parameter is an object that does not implement __toString() method'
            );
        }
    }
}
