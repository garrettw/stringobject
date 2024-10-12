<?php

namespace spec\StringObject;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use StringObject\Utf8String;

class Utf8StringSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('string of text');
        $this->shouldHaveType('StringObject\Utf8String');
    }

    function it_can_toarray()
    {
        $this->beConstructedWith('11/2/333');

        $result = $this->toArray()->getWrappedObject();
        if ($result[0][0] != '1') {
            throw new \Exception('Unexpected output ' . $result[0][0] . '; expected 1');
        }

        $result = $this->toArray('/')->getWrappedObject();
        if ($result[0] != '11') {
            throw new \Exception('Unexpected output ' . $result[0] . '; expected 11');
        }
        if (count(array_diff($result, [11,2,333])) != 0) {
            throw new \Exception('expected 0, got something else');
        }
    }

    function it_can_charat()
    {
        $this->beConstructedWith('abc');
        $this->charAt(0)->shouldBe('a');
        $this[0]->shouldBe('a');
    }

    function it_can_charcodeat()
    {
        $this->beConstructedWith("AÕ");
        $this->charCodeAt(0)->shouldBe(65);
        $this->charCodeAt(1)->shouldBe(213);
        $this->length()->shouldBe(2);
    }

    function it_can_length()
    {
        $this->beConstructedWith('test');
        $this->length()->shouldBe(4);
    }

    function it_can_append()
    {
        $this->beConstructedWith('one');
        $this->append('two')->raw->shouldBe('onetwo');
    }

    function it_can_concat()
    {
        $this->beConstructedWith('one');
        $this->concat('two')->raw->shouldBe('onetwo');
    }

    function it_can_escape()
    {
        $this->beConstructedWith("Is your name O'Reilly?");
        $this->escape()->raw->shouldBe("Is your name O\\'Reilly?");
    }

    function it_can_escape_cstyle()
    {
        $this->beConstructedWith('foo[ ]');
        $this->escape(Utf8String::C_STYLE, 'A..z')->raw->shouldBe('\\f\\o\\o\\[ \\]');
    }

    function it_can_escape_meta()
    {
        $this->beConstructedWith('Hello world. (can you hear me?)');
        $this->escape(Utf8String::META)->raw->shouldBe('Hello world\. \(can you hear me\?\)');
    }

    function it_can_hexencode()
    {
        $this->beConstructedWith('Hello');
        $this->hexEncode()->raw->shouldBe('48656c6c6f');
    }

    function it_can_hexdecode()
    {
        $this->beConstructedWith('48656c6c6f');
        $this->hexDecode()->raw->shouldBe('Hello');
    }

    function it_can_tokenize()
    {
        $this->beConstructedWith("This is\tan example\nstring");
        $this->resetToken();
        $this->nextToken(" \n\t")->raw->shouldBe('This');
        $this->nextToken(" \n\t")->raw->shouldBe('is');
        $this->nextToken(" \n\t")->raw->shouldBe('an');
        $this->resetToken();
        $this->nextToken(" \n\t")->raw->shouldBe('This');
    }

    function it_can_prepend()
    {
        $this->beConstructedWith('one');
        $this->prepend('two')->raw->shouldBe('twoone');
    }

    function it_can_remove()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->remove('mnrpqr', Utf8String::CASE_INSENSITIVE)->raw->shouldBe('ABCDEFGH://');
    }

    function it_can_repeat()
    {
        $this->beConstructedWith('-=');
        $this->repeat(3)->raw->shouldBe('-=-=-=');
    }

    function it_can_replace()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->replace('MNRPQR', 'bob')->raw->shouldBe('ABCDEFGH:/bob/');
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
