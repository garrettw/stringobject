<?php

namespace StringObject;

class HTMLStrObj extends TextStrObj
{
    public function nl2br($xhtml = true)
    {
        return new self(\nl2br($this->raw, $xhtml));
    }
}
