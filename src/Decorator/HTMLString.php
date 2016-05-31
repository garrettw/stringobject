<?php

namespace StringObject\Decorator;

class HTMLString extends TextString
{
    public function nl2br()
    {
        $this->anystring = $this->anystring->replaceWhole(\nl2br($this->anystring->raw, false));
        return $this;
    }

    public function nl2brX()
    {
        $this->anystring = $this->anystring->replaceWhole(\nl2br($this->anystring->raw, true));
        return $this;
    }
}
