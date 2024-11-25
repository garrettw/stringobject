<?php

namespace StringObject\Decorator;

use StringObject\StringObject;

class TextString
{
    protected StringObject $strobj;

    final public function __construct(StringObject $strobj)
    {
        $this->strobj = $strobj;
    }

    /**
     * @param string $name
     * @param mixed[] $args
     */
    public function __call(string $name, array $args): mixed
    {
        return $this->strobj->$name(...$args);
    }

    public function __toString(): string
    {
        return $this->strobj->__toString();
    }

    public function wordwrap(int $width = 75, string $break = "\n", bool $cutLongWords = false): static
    {
        return $this->duplicate(\wordwrap($this->__toString(), $width, $break, $cutLongWords));
    }

    protected function duplicate(string $str): static
    {
        $classname = \get_class($this->strobj);
        return new static(new $classname($str));
    }
}
