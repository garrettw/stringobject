<?php

namespace StringObject\Normalize;

interface Normalizer
{
    /**
     * @return int|string Either the normalized string, or an int indicating which form it already matches
     */
    public function normalize(\StringObject\Utf8String $utf8string);
}
