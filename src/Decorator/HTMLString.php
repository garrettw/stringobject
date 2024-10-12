<?php

namespace StringObject\Decorator;

class HTMLString extends TextString
{
    public function nl2br(bool $use_xhtml = true)
    {
        return $this->duplicate(\nl2br($this->__toString(), $use_xhtml));
    }
}
