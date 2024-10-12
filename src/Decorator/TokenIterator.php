<?php

namespace StringObject\Decorator;

use StringObject\StringObject;

class TokenIterator implements \Iterator
{
    private $strobj;
    private $delim;
    private $index = 0;
    private $curval;

    public function __construct(StringObject $strobj, string $delim)
    {
        $this->strobj = $strobj;
        $this->delim = $delim;
    }

    public function changeToken(string $delim)
    {
        $this->delim = $delim;
    }

    public function current(): mixed
    {
        return $this->curval;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function next(): void
    {
        $this->curval = $this->strobj->nextToken($this->delim);
        $this->index++;
    }

    public function rewind(): void
    {
        $this->strobj->resetToken();
        $this->curval = $this->strobj->nextToken($this->delim);
        $this->index = 0;
    }

    public function valid(): bool
    {
        return ($this->curval !== false);
    }
}
