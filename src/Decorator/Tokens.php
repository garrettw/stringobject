<?php

namespace StringObject\Decorator;

use StringObject\AnyString;

class Tokens implements \Iterator
{
    private $anystring;
    private $delim;
    private $index = 0;
    private $curval;

    public function __construct(AnyString $anystring, $delim)
    {
        $this->anystring = $anystring;
        $this->delim = $delim;
    }

    public function changeToken($delim)
    {
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
        $this->curval = $this->anystring->nextToken($this->delim);
        $this->index++;
    }

    public function rewind()
    {
        $this->anystring->resetToken();
        $this->curval = $this->anystring->nextToken($this->delim);
        $this->index = 0;
    }

    public function valid()
    {
        return ($this->curval !== false);
    }
}
