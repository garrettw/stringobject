<?php

namespace StringObject;

class AString extends AnyString
{
    protected $token = false;

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
        return new static($this->raw{$offset});
    }

    /**
     * @param integer $offset
     */
    public function charCodeAt($offset)
    {
        return \ord($this->raw{$offset});
    }

    public function compareTo($str, $flags = self::NORMAL, $length = 1)
    {
        // strip out bits we don't understand
        $flags &= (self::CASE_INSENSITIVE | self::CURRENT_LOCALE | self::NATURAL_ORDER | self::FIRST_N);

        $flagsmap = [
            self::NORMAL => 'strcmp',
            self::CASE_INSENSITIVE => 'strcasecmp',
            self::CURRENT_LOCALE => 'strcoll',
            self::NATURAL_ORDER => 'strnatcmp',
            (self::NATURAL_ORDER | self::CASE_INSENSITIVE) => 'strnatcasecmp',
            self::FIRST_N => 'strncmp',
            (self::FIRST_N | self::CASE_INSENSITIVE) => 'strncasecmp',
        ];

        if ($flags & self::FIRST_N) {
            return \call_user_func($flagsmap[$flags], $this->raw, $str, $length);
        }
        return \call_user_func($flagsmap[$flags], $this->raw, $str);
    }

    public function indexOf($needle, $offset = 0, $flags = self::NORMAL)
    {
        // strip out bits we don't understand
        $flags &= (self::REVERSE | self::CASE_INSENSITIVE);

        $flagsmap = [
            self::NORMAL => 'strpos',
            self::CASE_INSENSITIVE => 'stripos',
            self::REVERSE => 'strrpos',
            (self::REVERSE | self::CASE_INSENSITIVE) => 'strripos',
        ];
        return \call_user_func($flagsmap[$flags], $this->raw, $needle, $offset);
    }

    public function length()
    {
        return \strlen($this->raw);
    }

    public function substr($start, $length = 'omitted')
    {
        if ($length === 'omitted') {
            return new static(\substr($this->raw, $start));
        }
        return new static(\substr($this->raw, $start, $length));
    }

    public function nextToken($delim)
    {
        if ($this->token) {
            return new static(\strtok($delim));
        }
        $this->token = true;
        return new static(\strtok($this->raw, $delim));
    }

    // MODIFYING METHODS

    public function append($str)
    {
        return $this->replaceWhole($this->raw . $str);
    }

    public function chunk($length = 76, $ending = "\r\n")
    {
        return $this->replaceWhole(\chunk_split($this->raw, $length, $ending));
    }

    public function concat($str)
    {
        return $this->append($str);
    }

    public function escape($flags = self::NORMAL, $charlist = '')
    {
        // strip out bits we don't understand
        $flags &= (self::C_STYLE | self::META);

        $flagsmap = [
            self::NORMAL => 'addslashes',
            self::C_STYLE => 'addcslashes',
            self::META => 'quotemeta',
        ];
        if ($flags === self::C_STYLE) {
            return $this->replaceWhole(\call_user_func($flagsmap[$flags], $this->raw, $charlist));
        }
        return $this->replaceWhole(\call_user_func($flagsmap[$flags], $this->raw));
    }

    public function hexDecode()
    {
        return $this->replaceWhole(\hex2bin($this->raw));
    }

    public function hexEncode()
    {
        return $this->replaceWhole(\bin2hex($this->raw));
    }

    public function insertAt($str, $offset)
    {
        return $this->replaceSubstr($str, $offset, 0);
    }

    public function pad($newlength, $padding = ' ', $flags = self::END)
    {
        return $this->replaceWhole(\str_pad($this->raw, $newlength, $padding, $flags));
    }

    public function prepend($str)
    {
        return $this->replaceWhole($str . $this->raw);
    }

    public function remove($str, $flags = self::NORMAL)
    {
        return $this->replace($str, '', $flags);
    }

    public function removeSubstr($start, $length = null)
    {
        return $this->replaceSubstr('', $start, $length);
    }

    public function repeat($times)
    {
        return $this->replaceWhole(\str_repeat($this->raw, $times));
    }

    /**
     * @param string $replace
     */
    public function replace($search, $replace, $flags = self::NORMAL)
    {
        if ($flags & self::CASE_INSENSITIVE) {
            return $this->replaceWhole(\str_ireplace($search, $replace, $this->raw));
        }
        return $this->replaceWhole(\str_replace($search, $replace, $this->raw));
    }

    public function replaceSubstr($replacement, $start, $length = null)
    {
        if ($length === null) {
            $length = $this->length();
        }
        return $this->replaceWhole(\substr_replace($this->raw, $replacement, $start, $length));
    }

    public function resetToken()
    {
        $this->token = false;
    }

    public function reverse()
    {
        return $this->replaceWhole(\strrev($this->raw));
    }

    public function shuffle()
    {
        return $this->replaceWhole(\str_shuffle($this->raw));
    }

    public function times($times)
    {
        return $this->repeat($times);
    }

    public function translate($search, $replace = '')
    {
        if (is_array($search)) {
            return $this->replaceWhole(\strtr($this->raw, $search));
        }
        return $this->replaceWhole(\strtr($this->raw, $search, $replace));
    }

    public function trim($mask = " \t\n\r\0\x0B", $flags = self::BOTH_ENDS)
    {
        // strip out bits we don't understand
        $flags &= (self::END | self::BOTH_ENDS);

        $flagsmap = [
            self::START => 'ltrim',
            self::END => 'rtrim',
            self::BOTH_ENDS => 'trim',
        ];
        return $this->replaceWhole(\call_user_func($flagsmap[$flags], $this->raw, $mask));
    }

    public function unescape($flags = self::NORMAL)
    {
        // strip out bits we don't understand
        $flags &= (self::C_STYLE | self::META);

        $flagsmap = [
            self::NORMAL => 'stripslashes',
            self::C_STYLE => 'stripcslashes',
            self::META => 'stripslashes',
        ];
        return $this->replaceWhole(\call_user_func($flagsmap[$flags], $this->raw));
    }

    public function uuDecode()
    {
        return $this->replaceWhole(\convert_uudecode($this->raw));
    }

    public function uuEncode()
    {
        return $this->replaceWhole(\convert_uuencode($this->raw));
    }

    public function wordwrap($width = 75, $break = "\n")
    {
        return $this->replaceWhole(\wordwrap($this->raw, $width, $break, false));
    }

    public function wordwrapBreaking($width = 75, $break = "\n")
    {
        return $this->replaceWhole(\wordwrap($this->raw, $width, $break, true));
    }

    // TESTING METHODS

    public function contains($needle, $offset = 0, $flags = self::NORMAL)
    {
        if ($flags & self::EXACT_POSITION) {
            return ($this->indexOf($needle, $offset, $flags) === $offset);
        }
        return ($this->indexOf($needle, $offset, $flags) !== false);
    }

    public function countSubstr($needle, $offset = 0, $length = null)
    {
        if ($length === null) {
            return \substr_count($this->raw, $needle, $offset);
        }
        return \substr_count($this->raw, $needle, $offset, $length);
    }

    public function endsWith($str, $flags = self::NORMAL)
    {
        $flags &= self::CASE_INSENSITIVE;
        $offset = $this->length() - \strlen($str);
        return $this->contains($str, $offset, $flags | self::EXACT_POSITION | self::REVERSE);
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

    public function startsWith($str, $flags = self::NORMAL)
    {
        $flags &= self::CASE_INSENSITIVE;
        return $this->contains($str, 0, $flags | self::EXACT_POSITION);
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

    public function offsetGet($offset)
    {
        return $this->raw{$offset};
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Cannot assign ' . $value . ' to immutable AString instance at index ' . $offset);
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Cannot unset index ' . $offset . ' on immutable AString instance');
    }
}
