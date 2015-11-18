<?php

namespace spec\StringObject;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
        $this->beConstructedWith('A');
        $this->charCodeAt(0)->shouldBe(65);
    }

    function it_can_utf8codeat()
    {
        $this->beConstructedWith("Õ");
        $this->utf8CodeAt(0)->shouldBe(213);
    }

    function it_can_indexof()
    {
        $this->beConstructedWith('abcABC');
        $this->indexOf('a', 0, 0)->shouldBe(0);

        $this->indexOf('A', 0, 4)->shouldBe(0);
        $this->indexOf('a', 0, 8)->shouldBe(0);
        $this->indexOf('a', 0, 12)->shouldBe(3);
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

    function it_can_tokenize()
    {
        $this->beConstructedWith("This is\tan example\nstring");
        $this->resetToken();
        $this->nextToken(" \n\t")->raw->shouldBe('This');
        $this->nextToken(" \n\t")->raw->shouldBe('is');
        $this->nextToken(" \n\t")->raw->shouldBe('an');
    }

    function it_can_pad()
    {
        $this->beConstructedWith('Alien');
        $this->pad(10, '-=', 0)->raw->shouldBe('-=-=-Alien');
    }

    function it_can_prepend()
    {
        $this->beConstructedWith('one');
        $this->prepend('two')->raw->shouldBe('twoone');
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
        $this->beConstructedWith('string to shuffle');
        $this->shuffle();
        // finish
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
        $this->trim("\t ", 0)->raw->shouldBe('These are a few words :) ...  ');
        $this->trim("\t ", 1)->raw->shouldBe("\t\tThese are a few words :) ...");
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
}