<?php

namespace spec\StringObject;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UStringSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('string of text');
        $this->shouldHaveType('StringObject\UString');
    }

    function it_parses_one_char()
    {
        $this->beConstructedWith("\xC2\xBF");
        $this->charAt(0)->shouldBe("\xC2\xBF");
        $this->charCodeAt(0)->shouldBe(0xBF);
        $this->length()->shouldBe(1);
    }

    function it_leaves_one_ascii_alone()
    {
        $this->beConstructedWith("\x62");
        $this->charAt(0)->shouldBe(\chr(98));
        $this->charCodeAt(0)->shouldBe(98);
        $this->length()->shouldBe(1);
    }

    function it_translates_c1_chars()
    {
        $this->beConstructedWith("\x80");
        $this->charAt(0)->shouldBe("\xE2\x82\xAC");
    }

    function it_captures_utf8_chars()
    {
        $this->beConstructedWith("\xE2\x82\xAC\x62\xE2\x82\xAC");
        $this->charAt(0)->shouldBe("\xE2\x82\xAC");
        $this->charAt(1)->shouldBe("\x62");
        $this->charAt(2)->shouldBe("\xE2\x82\xAC");
    }

    function it_parses_1252_into_utf8()
    {
        // first char looks like beginning of 2-byte utf8 char,
        // but second char is not a continuation char,
        // so each should be parsed as single-byte non-utf8 chars
        $this->beConstructedWith("\xC2\x50");
        $this->charAt(0)->shouldBe("\xC3\x82"); // this one gets converted
        $this->charAt(1)->shouldBe("\x50");
    }

    function it_rejects_overlong()
    {
        $this->beConstructedWith("\xE0\x80\xBD"); // '=' in 3-byte overlong form
        $this->charAt(0)->shouldNotBe('='); // parses each byte as individual chars
        // because overlong should fail, not just be simplified
    }

    function it_rejects_bom_at_start()
    {
        $this->beConstructedWith("\xEF\xBB\xBF");
        $this->length()->shouldBe(0);
    }

    function it_converts_later_bom()
    {
        $this->beConstructedWith("abc\xEF\xBB\xBFabc");
        $this->charAt(3)->shouldBe("\xE2\x81\xA0");
    }
}
