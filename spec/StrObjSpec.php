<?php

namespace spec\StringObject;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use StringObject\StrObj;

class StrObjSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('string of text');
        $this->shouldHaveType('StringObject\StrObj');
    }

    function it_can_toarray()
    {
        $this->beConstructedWith('11/2/333');

        $result = $this->toArray()->getWrappedObject();
        if ($result[0] != '1') {
            throw new \Exception('Unexpected output ' . $result[0] . '; expected 1');
        }

        $result = $this->toArray('/')->getWrappedObject();
        if ($result[0] != '11') {
            throw new \Exception('Unexpected output ' . $result[0] . '; expected Hello');
        }
        if (count(array_diff($result, [11,2,333])) != 0) {
            throw new \Exception('expected 0, got something else');
        }
    }

    function it_can_charat()
    {
        $this->beConstructedWith('abc');
        $this->charAt(0)->raw->shouldBe('a');
        $this[0]->shouldBe('a');
    }

    function it_can_charcodeat()
    {
        $this->beConstructedWith("AÕ");
        $this->charCodeAt(0)->shouldBe(65);
        $this->charCodeAt(1)->shouldBe(213);
    }

    function it_can_compareto()
    {
        $this->beConstructedWith('hello9');
        $this->compareTo('Hello9')->shouldNotBe(0);
        $this->compareTo('Hello9', StrObj::CASE_INSENSITIVE)->shouldBe(0);
        $this->compareTo('Hello9', StrObj::CURRENT_LOCALE)->shouldNotBe(0);
        $this->compareTo('hello10', StrObj::NATURAL_ORDER)->shouldNotBe(0);
        $this->compareTo('helLO9', StrObj::FIRST_N, 3)->shouldBe(0);
        $this->compareTo('HELLO9', (StrObj::FIRST_N | StrObj::CASE_INSENSITIVE), 3)->shouldBe(0);
    }

    function it_can_indexof()
    {
        $this->beConstructedWith('abcABC');
        $this->indexOf('a', 0, StrObj::NORMAL)->shouldBe(0);
        $this->indexOf('A', 0, StrObj::CASE_INSENSITIVE)->shouldBe(0);
        $this->indexOf('a', 0, StrObj::REVERSE)->shouldBe(0);
        $this->indexOf('a', 0, (StrObj::REVERSE | StrObj::CASE_INSENSITIVE))->shouldBe(3);
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

    function it_can_asciify()
    {
        $this->beConstructedWith('πἳ');
        $this->asciify()->raw->shouldBe('pi');
    }

    function it_can_chunk()
    {
        $this->beConstructedWith('1234');
        $this->chunk(1, '-')->raw->shouldBe("1-2-3-4-");
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
        $this->escape(StrObj::C_STYLE, 'A..z')->raw->shouldBe('\\f\\o\\o\\[ \\]');
    }

    function it_can_escape_meta()
    {
        $this->beConstructedWith('Hello world. (can you hear me?)');
        $this->escape(StrObj::META)->raw->shouldBe('Hello world\. \(can you hear me\?\)');
    }

    function it_can_insertat()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->insertAt('bob', 9)->raw->shouldBe('ABCDEFGH:bob/MNRPQR/');
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

    function it_can_pad()
    {
        $this->beConstructedWith('Alien');
        $this->pad(10, '-=', StrObj::START)->raw->shouldBe('-=-=-Alien');
    }

    function it_can_prepend()
    {
        $this->beConstructedWith('one');
        $this->prepend('two')->raw->shouldBe('twoone');
    }

    function it_can_remove()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->remove('mnrpqr', StrObj::CASE_INSENSITIVE)->raw->shouldBe('ABCDEFGH://');
    }

    function it_can_removesubstr()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->removeSubstr(10, -1)->raw->shouldBe('ABCDEFGH://');
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

    function it_can_replacesubstr()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->replaceSubstr('bob', 10, -1)->raw->shouldBe('ABCDEFGH:/bob/');
    }

    function it_can_reverse()
    {
        $this->beConstructedWith('Hello world!');
        $this->reverse()->raw->shouldBe('!dlrow olleH');
    }

    function it_can_shuffle()
    {
        $this->beConstructedWith('1234');
        $result = $this->shuffle();

        if (!($result->contains('1') && $result->contains('2')
            && $result->contains('3') && $result->contains('4'))
        ) {
            throw new \Exception('Some original character(s) missing');
        }
    }

    function it_can_substr()
    {
        $this->beConstructedWith('abcdef');
        $this->substr(3)->raw->shouldBe('def');
        $this->substr(1, 3)->raw->shouldBe('bcd');
    }

    function it_can_times()
    {
        $this->beConstructedWith('-=');
        $this->times(3)->raw->shouldBe('-=-=-=');
    }

    function it_can_translate()
    {
        $this->beConstructedWith('hi all, I said hello');
        $this->translate(["h" => "-", "hello" => "hi", "hi" => "hello"])->raw->shouldBe('hello all, I said hi');
    }

    function it_can_trim()
    {
        $this->beConstructedWith("\t\tThese are a few words :) ...  ");
        $this->trim()->raw->shouldBe('These are a few words :) ...');
        $this->trim("\t ", StrObj::START)->raw->shouldBe('These are a few words :) ...  ');
        $this->trim("\t ", StrObj::END)->raw->shouldBe("\t\tThese are a few words :) ...");
    }

    function it_can_unescape()
    {
        $this->beConstructedWith("Is your name O\\'Reilly?");
        $this->unescape()->raw->shouldBe("Is your name O'Reilly?");
    }

    function it_can_unescape_cstyle()
    {
        $this->beConstructedWith('He\xallo');
        $this->unescape(StrObj::C_STYLE)->raw->shouldBe("He\nllo");
    }

    function it_can_unescape_meta()
    {
        $this->beConstructedWith('Hello world\. \(can you hear me\?\)');
        $this->unescape(StrObj::META)->raw->shouldBe('Hello world. (can you hear me?)');
    }

    function it_can_uudecode()
    {
        $this->beConstructedWith("+22!L;W9E(%!(4\"$`\n`");
        $this->uuDecode()->raw->shouldBe('I love PHP!');
    }

    function it_can_uuencode()
    {
        $this->beConstructedWith("test\ntext text\r\n");
        $this->uuEncode()->raw->shouldBe("0=&5S=`IT97AT('1E>'0-\"@``\n`\n");
    }

    function it_can_wordwrap()
    {
        $this->beConstructedWith('A very long woooooooooooord.');
        $this->wordwrap(8, "\n")->raw->shouldBe("A very\nlong\nwoooooooooooord.");
    }

    function it_can_wordwrapbreaking()
    {
        $this->beConstructedWith('A very long woooooooooooord.');
        $this->wordwrapBreaking(8, "\n")->raw->shouldBe("A very\nlong\nwooooooo\nooooord.");
    }

    function it_can_contains()
    {
        $this->beConstructedWith('abc');
        $this->contains('a')->shouldBe(true);
        $this->contains('A', 0, StrObj::CASE_INSENSITIVE)->shouldBe(true);
    }

    function it_can_contains_at()
    {
        $this->beConstructedWith('abc');
        $this->contains('a', 0, StrObj::EXACT_POSITION)->shouldBe(true);
        $this->contains('b', 0, StrObj::EXACT_POSITION)->shouldBe(false);
    }

    function it_can_countsubstr()
    {
        $this->beConstructedWith('This is a test');
        $this->countSubstr('is')->shouldBe(2);
        $this->countSubstr('is', 3)->shouldBe(1);
        $this->countSubstr('is', 3, 3)->shouldBe(0);
    }

    function it_can_endswith()
    {
        $this->beConstructedWith('abcdef abcxyz');
        $this->endsWith('xyz')->shouldBe(true);
        $this->endsWith('XYZ', StrObj::CASE_INSENSITIVE)->shouldBe(true);
    }

    function it_can_equals()
    {
        $this->beConstructedWith('test');
        $this->equals('test')->shouldBe(true);
    }

    function it_can_isascii()
    {
        $this->beConstructedWith('test');
        $this->isAscii()->shouldBe(true);
    }

    function it_can_test_empty_on_empty_string()
    {
        $this->beConstructedWith('');
        $this->isEmpty()->shouldBe(true);
    }

    function it_can_test_empty_on_nonempty_string()
    {
        $this->beConstructedWith('not empty');
        $this->isEmpty()->shouldBe(false);
    }

    function it_can_startswith()
    {
        $this->beConstructedWith('abcdef abcxyz');
        $this->startsWith('abc')->shouldBe(true);
        $this->startsWith('AbC', StrObj::CASE_INSENSITIVE)->shouldBe(true);
    }
}