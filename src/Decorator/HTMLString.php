<?php

namespace StringObject\Decorator;

class HTMLString extends TextString
{
    public function nl2br()
    {
        return $this->replaceWhole(\nl2br($this->raw, false));
    }

    public function nl2brX()
    {
        return $this->replaceWhole(\nl2br($this->raw, true));
    }
}
