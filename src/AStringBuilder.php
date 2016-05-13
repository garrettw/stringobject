<?php

namespace StringObject;

class AStringBuilder extends AString
{
    public function replaceWhole($replacement = '')
    {
        self::testStringableObject($replacement);
        $this->raw = (string) $replacement;
        return $this;
    }
}
