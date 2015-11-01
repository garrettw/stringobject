<?php

namespace spec\StringObject;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StrObjSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('string of text');
        $this->shouldHaveType('StringObject\String');
    }

    function it_can_addcslashes()
    {
        $this->beConstructedWith('foo[ ]');
        $this->addcslashes('A..z')->raw->shouldBe('\\f\\o\\o\\[ \\]');
    }

    function it_can_addslashes()
    {
        $this->beConstructedWith("Is your name O'Reilly?");
        $this->addslashes()->raw->shouldBe("Is your name O\\'Reilly?");
    }

    function it_can_bin2hex()
    {
        $this->beConstructedWith("\371");
        $this->bin2hex()->raw->shouldBe("f9");
    }

    function it_can_chop()
    {
        $this->beConstructedWith("\t\tThese are a few words :) ...  ");
        $this->chop(' .')->raw->shouldBe("\t\tThese are a few words :)");
    }

    function it_can_chunkSplit()
    {
        $this->beConstructedWith('1234');
        $this->chunkSplit(1, '-')->raw->shouldBe("1-2-3-4-");
        $this->chunk_split(1, '-')->raw->shouldBe("1-2-3-4-");
    }

    function it_can_convertCyrillic()
    {
        $this->beConstructedWith("\xC0");
        $this->convertCyrillic('w', 'k')->raw->shouldBe("\xE1");
        $this->convert_cyr_string('w', 'k')->raw->shouldBe("\xE1");
    }

    function it_can_uudecode()
    {
        $this->beConstructedWith("!,0``...");
        $this->uudecode()->raw->shouldBe('1');
        $this->convert_uudecode()->raw->shouldBe('1');
    }

    function it_can_uuencode()
    {
        $this->beConstructedWith('1');
        $this->uuencode()->uudecode()->raw->shouldBe('1');
    }

    function it_can_crc32()
    {
        $this->beConstructedWith('1');
        $this->crc32()->shouldBe(2212294583);
    }


}