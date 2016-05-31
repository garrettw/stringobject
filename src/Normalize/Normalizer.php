<?php

namespace StringObject\Normalize;

interface Normalizer
{
    public function normalize(\StringObject\UString $ustring);
}