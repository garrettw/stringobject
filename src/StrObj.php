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

    // PROPERTIES

    protected $raw;
    protected $token = false;
    protected $caret = 0;

    // STATIC FUNCTIONS

    public static function make($thing)
    {
        return new self($thing);
    }

    // MAGIC METHODS

    public function __construct($thing)
    {
        self::testStringableObject($thing);

        if (is_array($thing)) {
            throw new \InvalidArgumentException('Unsure of how to convert array to string');
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
        $code = \ord($this->raw{$offset});

        if ($code > 191 && $code < 248) {
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
}
