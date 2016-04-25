<?php

namespace Spipu\Html2Pdf\Tests\Css\Parser;

use Spipu\Html2Pdf\Css\Parser\ClassSelectorParser;

/**
 * Class ClassSelectorParserTest
 */
class ClassSelectorParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $text
     * @param $matchedText
     *
     * @dataProvider matchProvider
     */
    public function testMatch($text, $matchedText)
    {
        $selector = new ClassSelectorParser();
        $result = $selector->match($text);

        if ($matchedText == false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('Spipu\Html2Pdf\Css\Selector\ClassSelector', $result);
            $this->assertEquals($matchedText, $result->getText());
        }
    }

    public function matchProvider()
    {
        return array(
            array('#myid', false),
            array('#myid .myclass', false),
            array('.myclass', '.myclass'),
            array('.myclass .myother', '.myclass'),
        );
    }
}
