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

    function it_can_chunksplit()
    {
        $this->beConstructedWith('1234');
        $this->chunkSplit(1, '-')->raw->shouldBe("1-2-3-4-");
        $this->chunk_split(1, '-')->raw->shouldBe("1-2-3-4-");
    }

    function it_can_convertcyrillic()
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

    function it_can_countchars()
    {
        $this->beConstructedWith('Two Ts and one F.');
        $result = $this->countChars(1)->getWrappedObject();

        if ($result[ord('T')] != 2) {
            throw new \Exception('Result is not 2 but rather '.$result[ord('T')]);
        }
    }

    function it_can_crc32()
    {
        $this->beConstructedWith('1');
        $this->crc32()->shouldBe(2212294583);
    }

    function it_can_crypt()
    {
        $this->beConstructedWith('rasmuslerdorf');
        $this->crypt('$2a$07$usesomesillystringforsalt$')->raw
            ->shouldBe('$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi');
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

    function it_can_explode()
    {
        $this->beConstructedWith('1/22/333');
        if (count(array_diff($this->explode('/')->getWrappedObject(), [1,22,333])) != 0) {
            throw new \Exception('expected 0, got something else');
        }
    }

    function it_can_hebrev()
    {
        $this->beConstructedWith('בְּרֵאשִׁית, בָּרָא אֱלֹהִים, אֵת הַשָּׁמַיִם, וְאֵת הָאָרֶץ.');
        $this->hebrev()->raw->shouldBe('.בְּרֵאשִׁית, בָּרָא אֱלֹהִים, אֵת הַשָּׁמַיִם, וְאֵת הָאָרֶץ');
    }

    function it_can_hebrevc()
    {
        $this->beConstructedWith("בְּרֵאשִׁית, בָּרָא אֱלֹהִים, אֵת הַשָּׁמַיִם, וְאֵת הָאָרֶץ.\n");
        $this->hebrev()->raw->shouldBe(".בְּרֵאשִׁית, בָּרָא אֱלֹהִים, אֵת הַשָּׁמַיִם, וְאֵת הָאָרֶץ\n");
    }

    function it_can_hex2bin()
    {
        $this->beConstructedWith('6578616d706c65206865782064617461');
        $this->hex2bin()->raw->shouldBe('example hex data');
    }

    function it_can_htmlentitydecode()
    {
        $this->beConstructedWith("I'll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now");
        $this->htmlEntityDecode()->raw->shouldBe('I\'ll "walk" the <b>dog</b> now');
    }

    function it_can_htmlentityencode()
    {
        $this->beConstructedWith('I\'ll "walk" the <b>dog</b> now');
        $this->htmlEntityEncode()->raw->shouldBe("I'll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now");
        $this->htmlentities()->raw->shouldBe("I'll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now");
    }

    function it_can_htmlspecialcharsdecode()
    {
        $this->beConstructedWith("<p>this -&gt; &quot;</p>\n");
        $this->htmlSpecialCharsDecode()->raw->shouldBe("<p>this -> \"</p>\n");
        $this->htmlSpecialCharsDecode(ENT_NOQUOTES)->raw->shouldBe("<p>this -> &quot;</p>\n");
    }

    function it_can_firstchartolowercase()
    {
        $this->beConstructedWith('Hi!');
        $this->firstCharToLowerCase()->raw->shouldBe('hi!');
    }

    function it_can_levenshtein()
    {
        $this->beConstructedWith('carrrot');
        $words  = ['apple', 'pineapple', 'banana', 'orange',
                    'radish', 'carrot', 'pea', 'bean', 'potato'];
        $shortest = -1;
        foreach ($words as $word) {
            $lev = $this->levenshtein($word)->getWrappedObject();
            if ($lev == 0) {
                $closest = $word;
                $shortest = 0;
                break;
            }
            if ($lev <= $shortest || $shortest < 0) {
                $closest  = $word;
                $shortest = $lev;
            }
        }
        if ($closest != 'carrot') {
            throw new \Exception("wrong result: $closest");
        }
    }

    function it_can_ltrim()
    {
        $this->beConstructedWith('Hello World');
        $this->ltrim('Hdle')->raw->shouldBe('o World');
    }

    function it_can_md5()
    {
        $this->beConstructedWith('apple');
        $this->md5()->raw->shouldBe('1f3870be274f6c49b3e31a0c6728957f');
    }

    function it_can_metaphone()
    {
        $this->beConstructedWith('programming');
        $this->metaphone()->raw->shouldBe('PRKRMNK');
        $this->metaphone(5)->raw->shouldBe('PRKRM');
    }

    function it_can_nl2br()
    {
        $this->beConstructedWith("line one\ntwo\nthree");
        $this->nl2br()->raw->shouldBe("line one<br />\ntwo<br />\nthree");
        $this->nl2br(false)->raw->shouldBe("line one<br>\ntwo<br>\nthree");
    }

    function it_can_ord()
    {
        $this->beConstructedWith("\n");
        $this->ord()->shouldBe(10);
    }

    function it_can_quotedprintabledecode()
    {
        $this->beConstructedWith('=3D');
        $this->quotedPrintableDecode()->raw->shouldBe('=');
    }

    function it_can_quotedprintableencode()
    {
        $this->beConstructedWith('=');
        $this->quotedPrintableEncode()->raw->shouldBe('=3D');
    }

    function it_can_quotemeta()
    {
        $this->beConstructedWith('Hello world. (can you hear me?)');
        $this->quotemeta()->raw->shouldBe('Hello world\. \(can you hear me\?\)');
    }

    function it_can_rtrim()
    {
        $this->beConstructedWith('Hello World');
        $this->rtrim('Hdle')->raw->shouldBe('Hello Wor');
    }

    function it_can_sha1()
    {
        $this->beConstructedWith('apple');
        $this->sha1()->raw->shouldBe('d0be2dc421be4fcd0172e5afceea3970e2f3d940');
    }

    function it_can_similartext()
    {
        $this->beConstructedWith('Hello');
        $this->similarText('hello')->shouldBe(4);
    }

    function it_can_soundex()
    {
        $this->beConstructedWith('Gauss');
        $this->soundex()->raw->shouldBe('G200');
    }

    function it_can_sscanf()
    {
        $this->beConstructedWith('00ccff');
        $result = $this->sscanf('%2x%2x%2x')->getWrappedObject();

        if (count($result) != 3 && $result[0] != 0) {
            throw new \Exception('Unexpected result 0: '.$result[0]);
        }
    }

    function it_can_getcsv()
    {
        $this->beConstructedWith('"text",1,"two",3');
        $result = $this->getCSV()->getWrappedObject();

        if (count($result) != 4 && $result[0] != 'text') {
            throw new \Exception('Unexpected result 0: '.$result[0]);
        }
    }

    function it_can_pad()
    {
        $this->beConstructedWith('Alien');
        $this->pad(10, "-=", STR_PAD_LEFT)->raw->shouldBe('-=-=-Alien');
    }

    function it_can_repeat()
    {
        $this->beConstructedWith('-=');
        $this->repeat(3)->raw->shouldBe('-=-=-=');
    }

    function it_can_times()
    {
        $this->beConstructedWith('-=');
        $this->times(3)->raw->shouldBe('-=-=-=');
    }

    function it_can_rot13()
    {
        $this->beConstructedWith('PHP 4.3.0');
        $this->rot13()->raw->shouldBe('CUC 4.3.0');
    }

    function it_can_shuffle()
    {
        $this->beConstructedWith('string to shuffle');
        $this->shuffle();
        // finish
    }

    function it_can_split()
    {
        $this->beConstructedWith('Hello Friend');
        $result = $this->split(3)->getWrappedObject();

        if ($result[0] != 'Hel') {
            throw new \Exception('Unexpected output');
        }
    }

    function it_can_toArray()
    {
        $this->it_can_split();
    }

    function it_can_countwords()
    {
        $this->beConstructedWith("Hello fri3nd, you're\n       looking          good today!");
        $this->countWords(0, '3')->shouldBe(6);
    }

    function it_can_icompare()
    {
        $this->beConstructedWith('Hello');
        $this->icompare('hello')->shouldBe(0);
    }

    function it_can_substrfromchartoend()
    {
        $this->beConstructedWith('name@example.com');
        $this->substrFromCharToEnd('@')->raw->shouldBe('@example.com');
        $this->substrFromCharToEnd('@', true)->raw->shouldBe('name');
    }

    function it_can_compare()
    {
        $this->beConstructedWith('Hello');
        $this->compare('hello')->shouldNotBe(0);
    }

    function it_can_comparelocale()
    {
        $this->beConstructedWith('a');
        $this->compareLocale('A')->shouldBe(32);
    }

    function it_can_lengthbeforecharmask()
    {
        $this->beConstructedWith('hello');
        $this->lengthBeforeCharMask('world')->shouldBe(2);
    }

    function it_can_striptags()
    {
        $this->beConstructedWith('<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>');
        $this->stripTags('<p><a>')->raw->shouldBe('<p>Test paragraph.</p> <a href="#fragment">Other text</a>');
    }

    function it_can_stripcslashes()
    {
        $this->beConstructedWith("\\'\\'");
        $this->stripcslashes()->raw->shouldBe('\'\'');
    }

    function it_can_iindexof()
    {
        $this->beConstructedWith('XYZ');
        $this->iindexOf('x')->shouldBe(0);
    }

    function it_can_stripslashes()
    {
        $this->beConstructedWith("\\'\\'");
        $this->stripslashes()->raw->shouldBe('\'\'');
    }

    function it_can_isubstrfromstringtoend()
    {
        $this->beConstructedWith('Hello World');
        $this->isubstrFromStringToEnd('LO')->raw->shouldBe('lo World');
        $this->isubstrFromStringToEnd('LO', true)->raw->shouldBe('Hel');
    }

    function it_can_length()
    {
        $this->beConstructedWith('test');
        $this->length()->shouldBe(4);
    }

    function it_can_icomparenatural()
    {
        $this->beConstructedWith('Hello');
        $this->icompareNatural('hello')->shouldBe(0);
    }

    function it_can_comparenatural()
    {
        $this->beConstructedWith('Hello');
        $this->compare('hello')->shouldNotBe(0);
    }

    function it_can_icomparefirstn()
    {
        $this->beConstructedWith('Hello');
        $this->icompareFirstN('hello', 2)->shouldBe(0);
    }

    function it_can_comparefirstn()
    {
        $this->beConstructedWith('Hello');
        $this->compareFirstN('hello', 2)->shouldNotBe(0);
    }

    function it_can_substrfromcharlisttoend()
    {
        $this->beConstructedWith('This is a Simple text.');
        $this->substrFromCharListToEnd('mi')->raw->shouldBe('is is a Simple text.');
    }

    function it_can_indexof()
    {
        $this->beConstructedWith('abc');
        $this->indexOf('a')->shouldBe(0);
        $this->indexOf('a')->shouldNotBe(false);
    }

    function it_can_substrfromlastchartoend()
    {
        $this->beConstructedWith("Line 1\nLine 2\nLine 3");
        $this->substrFromLastCharToEnd("\n")->raw->shouldBe("\nLine 3");
    }

    function it_can_reverse()
    {
        $this->beConstructedWith('Hello world!');
        $this->reverse()->raw->shouldBe('!dlrow olleH');
    }

    function it_can_iindexoflast()
    {
        $this->beConstructedWith('ababcd');
        $this->iindexOfLast('aB')->shouldBe(2);
    }

    function it_can_indexoflast()
    {
        $this->beConstructedWith('0123456789a123456789b123456789c');
        $this->indexOfLast('7', -5)->shouldBe(17);
        $this->indexOfLast('7', 20)->shouldBe(27);
        $this->indexOfLast('7', 28)->shouldBe(false);
    }

    function it_can_lengthofmasked()
    {
        $this->beConstructedWith('42 is the answer to the 128th question.');
        $this->lengthOfMasked('1234567890')->shouldBe(2);
    }

    function it_can_substrfromstringtoend()
    {
        $this->beConstructedWith('name@example.com');
        $this->substrFromStringToEnd('@')->raw->shouldBe('@example.com');
        $this->substrFromStringToEnd('@', true)->raw->shouldBe('name');
    }

    function it_can_tolowercase()
    {
        $this->beConstructedWith('Mary Had A Little Lamb and She LOVED It So');
        $this->toLowerCase()->raw->shouldBe('mary had a little lamb and she loved it so');
    }

    function it_can_tokenize()
    {
        $this->beConstructedWith("This is\tan example\nstring");
        $this->tokenize(" \n\t")->raw->shouldBe('This');
        $this->nextToken(" \n\t")->raw->shouldBe('is');
        $this->nextToken(" \n\t")->raw->shouldBe('an');
    }

    function it_can_touppercase()
    {
        $this->beConstructedWith('Mary Had A Little Lamb and She LOVED It So');
        $this->toUpperCase()->raw->shouldBe('MARY HAD A LITTLE LAMB AND SHE LOVED IT SO');
    }

    function it_can_translate()
    {
        $this->beConstructedWith('hi all, I said hello');
        $this->translate(["h" => "-", "hello" => "hi", "hi" => "hello"])->raw->shouldBe('hello all, I said hi');
    }

    function it_can_comparesubstr()
    {
        $this->beConstructedWith('abcde');
        $this->compareSubstr('bc', 1, 2)->shouldBe(0);
    }

    function it_can_countsubstr()
    {
        $this->beConstructedWith('This is a test');
        $this->countSubstr('is')->shouldBe(2);
    }

    function it_can_replace()
    {
        $this->beConstructedWith('ABCDEFGH:/MNRPQR/');
        $this->replace('bob', 10, -1)->raw->shouldBe('ABCDEFGH:/bob/');
    }

    function it_can_substr()
    {
        $this->beConstructedWith('abcdef');
        $this->substr(1, 3)->raw->shouldBe('bcd');
    }

    function it_can_trim()
    {
        $this->beConstructedWith("\t\tThese are a few words :) ...  ");
        $this->trim()->raw->shouldBe('These are a few words :) ...');
    }

    function it_can_firstchartouppercase()
    {
        $this->beConstructedWith('hello world!');
        $this->firstCharToUpperCase()->raw->shouldBe('Hello world!');
    }

    function it_can_wordstouppercase()
    {
        $this->beConstructedWith('hello world!');
        $this->wordsToUpperCase()->raw->shouldBe('Hello World!');
    }

    function it_can_wordwrap()
    {
        $this->beConstructedWith('A very long woooooooooooord.');
        $this->wordwrap(8, "\n", true)->raw->shouldBe("A very\nlong\nwooooooo\nooooord.");
    }
}