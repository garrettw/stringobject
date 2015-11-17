<?php

namespace StringObject;

class HTMLStrObj extends TextStrObj
{
    public function nl2br()
    {
        return new self(\nl2br($this->raw, false));
    }

    public function nl2brX()
    {
        return new self(\nl2br($this->raw, true));
    }
}
