<?php

namespace StringObject;

class Chunks implements \ArrayAccess, \Countable, \Iterator
{
    private $strobj;
    private $length;
    private $ending;
    private $index = 0;

    public function __construct(StrObj $so, $length = 76, $ending = "\r\n")
    {
        $this->strobj = $so;
        $this->length = $length;
        $this->ending = $ending;
    }

    public function count()
    {
        return \ceil($this->strobj->length() / ($this->length + 0.0));
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
        return ($this->index * $this->length < $this->strobj->length());
    }

    public function offsetExists($index)
    {
        $index = (int) $index;
        return ($index >= 0 && $index * $this->length < $this->strobj->length());
    }

    public function offsetGet($index)
    {
        $offset = $index * $this->length;
        return $this->strobj->substr(
            $offset,
            \min($offset + $this->length, $this->strobj->length() - $offset)
        )->append($this->ending);
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Invalid assignment operation on StrObj adapter');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Invalid unset operation on StrObj adapter');
    }
}
