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
        if ($type == 'token') {
            return new TokenIterator($so, $delim);
        }
        return new CharIterator($so);
    }
}
