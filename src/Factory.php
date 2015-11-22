<?php

namespace StringObject;

class Factory
{
    private $encoding;

    public function __construct($enc = StrObj::WINDOWS1252)
    {
        $this->encoding = $enc;
    }

    public function make($str)
    {
        return new StrObj($str, $this->encoding);
    }
}
