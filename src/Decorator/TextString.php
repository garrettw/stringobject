<?php

namespace StringObject\Decorator;

use StringObject\AnyString;

class TextString
{
    private $anystring;

    public function __construct(AnyString $anystring)
    {
        $this->anystring = $anystring;
    }
}
