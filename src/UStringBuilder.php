<?php

namespace StringObject;

class UStringBuilder extends UString
{
    public function replaceWhole($replacement = '')
    {
        self::testStringableObject($replacement);
        $this->raw = (string) $replacement;
        return $this;
    }
}
