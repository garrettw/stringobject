<?php

namespace StringObject\Decorator;

use StringObject\StringObject;

class TextString
{
    protected $strobj;

    final public function __construct(StringObject $strobj)
    {
        $this->strobj = $strobj;
    }

    public function __call($name, $args): mixed
    {
        return $this->strobj->$name($args);
    }

    public function __toString(): string
    {
        return $this->strobj->__toString();
    }

    public function wordwrap(int $width = 75, string $break = "\n", bool $cut_long_words = false)
    {
        return $this->duplicate(\wordwrap($this->__toString(), $width, $break, $cut_long_words));
    }

    protected function duplicate(string $str)
    {
        $classname = \get_class($this->strobj);
        return new static(new $classname($str));
    }
}
