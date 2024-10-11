<?php

namespace StringObject\Decorator;

class HTMLString extends TextString
{
    public function nl2br(bool $use_xhtml = true)
    {
        $this->strobj = $this->strobj->replaceWhole(\nl2br($this->strobj->raw, $use_xhtml));
        return $this;
    }
}
