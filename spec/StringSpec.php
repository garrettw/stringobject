<?php

namespace spec\Noair;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('string of text');
        $this->shouldHaveType('StrObj\String');
    }
}