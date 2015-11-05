<?php

namespace StringObject;

class CharIterator implements \Iterator
{
    private $strobj;
    private $index = 0;

    public function __construct(StrObj $so)
    {
        $this->strobj = $so;
    }

    public function current()
    {
        return $this->strobj[$this->index];
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
        return ($this->index < $this->strobj->length());
    }
}
