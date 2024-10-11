<?php

namespace StringObject;

class Utf8StringBuilder extends Utf8String
{
    public function replaceWhole($replacement = '')
    {
        static::stringableOrFail($replacement);
        $this->raw = (string) $replacement;
        return $this;
    }
}
