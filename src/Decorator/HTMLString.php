<?php

namespace StringObject\Decorator;

class HTMLString extends TextString
{
    public function nl2br(bool $useXhtml = true)
    {
        return $this->duplicate(\nl2br($this->__toString(), $useXhtml));
    }
}
