<?php

namespace Spipu\Html2Pdf\Tests\Css;

use Spipu\Html2Pdf\Css\Selector\IdSelectorParser;

/**
 * Class IdSelectorParserTest
 */
class IdSelectorParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $text
     * @param $matchedText
     *
     * @dataProvider matchProvider
     */
    public function testMatch($text, $matchedText)
    {
        $selector = new IdSelectorParser();
        $result = $selector->match($text);

        if ($matchedText == false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('Spipu\Html2Pdf\Css\Selector\IdSelector', $result);
            $this->assertEquals($matchedText, $result->getText());
        }
    }

    public function matchProvider()
    {
        return array(
            array('#myid', '#myid'),
            array('#myid .myclass', '#myid'),
            array('#myid #myother', '#myid'),
            array('.myclass', false),
            array('.class #myid', false),
        );
    }
}
