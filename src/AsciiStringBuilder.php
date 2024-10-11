<?php

namespace StringObject;

class AsciiStringBuilder extends AsciiString
{
    public function replaceWhole($replacement = '')
    {
        static::stringableOrFail($replacement);
        $this->raw = (string) $replacement;
        return $this;
    }
}
