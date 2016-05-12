<?php

namespace StringObject;

class AStringBuilder extends AString
{
    // MODIFYING METHODS

    public function append($str)
    {
        $this->raw .= $str;
        return $this;
    }

    public function chunk($length = 76, $ending = "\r\n")
    {
        $this->raw = \chunk_split($this->raw, $length, $ending);
        return $this;
    }

    public function escape($flags = self::NORMAL, $charlist = '')
    {
        $flagsmap = [
            self::NORMAL => 'addslashes',
            self::C_STYLE => 'addcslashes',
            self::META => 'quotemeta',
        ];
        if ($flags === self::C_STYLE) {
            $this->raw = \call_user_func($flagsmap[$flags], $this->raw, $charlist);
            return $this;
        }
        $this->raw = \call_user_func($flagsmap[$flags], $this->raw);
        return $this;
    }

    public function pad($newlength, $padding = ' ', $flags = self::END)
    {
        $this->raw = \str_pad($this->raw, $newlength, $padding, $flags);
        return $this;
    }

    public function prepend($str)
    {
        $this->raw = $str . $this->raw;
        return $this;
    }

    public function repeat($times)
    {
        $this->raw = \str_repeat($this->raw, $times);
        return $this;
    }

    /**
     * @param string $replace
     */
    public function replace($search, $replace, $flags = self::NORMAL)
    {
        if ($flags & self::CASE_INSENSITIVE) {
            $this->raw = \str_ireplace($search, $replace, $this->raw);
            return $this;
        }
        $this->raw = \str_replace($search, $replace, $this->raw);
        return $this;
    }

    public function replaceSubstr($replacement, $start, $length = null)
    {
        if ($length === null) {
            $length = $this->length();
        }
        $this->raw = \substr_replace($this->raw, $replacement, $start, $length);
        return $this;
    }

    public function replaceWhole($replacement = '')
    {
        self::testStringableObject($replacement);
        $this->raw = (string) $replacement;
        return $this;
    }

    public function reverse()
    {
        $this->raw = \strrev($this->raw);
        return $this;
    }

    public function shuffle()
    {
        $this->raw = \str_shuffle($this->raw);
        return $this;
    }

    public function translate($search, $replace = '')
    {
        if (is_array($search)) {
            $this->raw = \strtr($this->raw, $search);
            return $this;
        }
        $this->raw = \strtr($this->raw, $search, $replace);
        return $this;
    }

    public function trim($mask = " \t\n\r\0\x0B", $flags = self::BOTH_ENDS)
    {
        $flagsmap = [
            self::START => 'ltrim',
            self::END => 'rtrim',
            self::BOTH_ENDS => 'trim',
        ];
        $this->raw = \call_user_func($flagsmap[$flags], $this->raw, $mask);
        return $this;
    }

    public function unescape($flags = self::NORMAL)
    {
        $flagsmap = [
            self::NORMAL => 'stripslashes',
            self::C_STYLE => 'stripcslashes',
            self::META => 'stripslashes',
        ];
        $this->raw = \call_user_func($flagsmap[$flags], $this->raw);
        return $this;
    }

    public function uuDecode()
    {
        $this->raw = \convert_uudecode($this->raw);
        return $this;
    }

    public function uuEncode()
    {
        $this->raw = \convert_uuencode($this->raw);
        return $this;
    }

    public function wordwrap($width = 75, $break = "\n")
    {
        $this->raw = \wordwrap($this->raw, $width, $break, false);
        return $this;
    }

    public function wordwrapBreaking($width = 75, $break = "\n")
    {
        $this->raw = \wordwrap($this->raw, $width, $break, true);
        return $this;
    }
}
