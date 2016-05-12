<?php

namespace StringObject\Decorator;

use StringObject\AnyString;

class TextString
{
    protected $anystring;

    public function __construct(AnyString $anystring)
    {
        $this->anystring = $anystring;
    }
}
