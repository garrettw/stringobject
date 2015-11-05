<?php

namespace StringObject;

class TokenIterator implements \Iterator
{
    private $strobj;
    private $delim;
    private $index = 0;
    private $curval;

    public function __construct(StrObj $so, $delim)
    {
        $this->strobj = $so;
        $this->delim = $delim;
    }

    public function current()
    {
        return $this->curval;
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->curval = $this->strobj->strtok($this->delim);
        $this->index++;
    }

    public function rewind()
    {
        $this->strobj->resetToken();
        $this->curval = $this->strobj->strtok($this->delim);
        $this->index = 0;
    }

    public function valid()
    {
        return ($this->curval !== false);
    }

    public function changeToken($delim)
    {
        $this->delim = $delim;
    }
}
