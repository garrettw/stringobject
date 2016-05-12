<?php

namespace StringObject\Decorator;

use StringObject\AnyString;

class Chunks implements \ArrayAccess, \Countable, \Iterator
{
    private $anystring;
    private $length;
    private $ending;
    private $index = 0;

    public function __construct(AnyString $anystring, $length = 76, $ending = "\r\n")
    {
        $this->anystring = $anystring;
        $this->length = $length;
        $this->ending = $ending;
    }

    public function count()
    {
        return \ceil($this->anystring->length() / ($this->length + 0.0));
    }

    public function current()
    {
        return $this->offsetGet($this->index);
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->index++;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return ($this->index * $this->length < $this->anystring->length());
    }

    public function offsetExists($index)
    {
        $index = (int) $index;
        return ($index >= 0 && $index * $this->length < $this->anystring->length());
    }

    public function offsetGet($index)
    {
        $offset = $index * $this->length;
        return $this->anystring->substr(
            $offset,
            \min($offset + $this->length, $this->anystring->length() - $offset)
        )->append($this->ending);
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Cannot assign ' . $value
            .' to immutable AnyString adapter instance at index ' . $offset);
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Cannot unset index ' . $offset . ' on immutable AnyString adapter instance');
    }
}
