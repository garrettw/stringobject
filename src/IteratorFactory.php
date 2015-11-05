<?php

namespace StringObject;

class IteratorFactory
{
    private $type;
    private $delim;

    public function __construct($type = 'char', $delim = '')
    {
        $this->type = $type;
        $this->delim = $delim;
    }

    public function makeFor(StrObj $so)
    {
        if ($this->type == 'token') {
            return new TokenIterator($so, $this->delim);
        }
        return new CharIterator($so);
    }
}
