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

    }

    function it_can_split()
    {

    }

    function it_can_toArray()
    {
        // same as above
    }

    function it_can_countwords()
    {

    }

    function it_can_icompare()
    {

    }

    function it_can_substringfromchartoend()
    {

    }

    function it_can_compare()
    {

    }

    function it_can_comparelocale()
    {

    }

    function it_can_lengthbeforecharmask()
    {

    }

    function it_can_striptags()
    {

    }

    function it_can_stripcslashes()
    {

    }

    function it_can_iindexof()
    {

    }

    function it_can_stripslashes()
    {

    }

    function it_can_isubstrfromstringtoend()
    {

    }

    function it_can_length()
    {

    }

    function it_can_icomparenatural()
    {

    }

    function it_can_comparenatural()
    {

    }

    function it_can_icomparefirstn()
    {

    }

    function it_can_comparefirstn()
    {

    }

    function it_can_substrfromcharlisttoend()
    {

    }

    function it_can_indexof()
    {

    }

    function it_can_substrfromlastchartoend()
    {

    }

    function it_can_reverse()
    {

    }

    function it_can_iindexoflast()
    {

    }

    function it_can_indexoflast()
    {

    }

    function it_can_lengthofmasked()
    {

    }

    function it_can_substrfromstringtoend()
    {

    }

    function it_can_tolowercase()
    {

    }

    function it_can_tokenize()
    {

    }

    function it_can_touppercase()
    {

    }

    function it_can_translate()
    {

    }

    function it_can_comparesubstr()
    {

    }

    function it_can_countsubstr()
    {

    }

    function it_can_replace()
    {

    }

    function it_can_substr()
    {

    }

    function it_can_trim()
    {

    }

    function it_can_firstchartouppercase()
    {

    }

    function it_can_wordstouppercase()
    {

    }

    function it_can_wordwrap()
    {

    }
}