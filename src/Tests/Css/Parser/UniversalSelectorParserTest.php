<?php

namespace Spipu\Html2Pdf\Tests\Css\Parser;

use Spipu\Html2Pdf\Css\Parser\UniversalSelectorParser;

/**
 * Class UniversalSelectorParserTest
 */
class UniversalSelectorParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $text
     * @param $matchedText
     *
     * @dataProvider matchProvider
     */
    public function testMatch($text, $matchedText)
    {
        $selector = new UniversalSelectorParser();
        $result = $selector->match($text);

        if ($matchedText == false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('Spipu\Html2Pdf\Css\Selector\UniversalSelector', $result);
            $this->assertEquals($matchedText, $result->getText());
        }
    }

    public function matchProvider()
    {
        return array(
            array('*', '*'),
            array('*.myclass', '*'),
            array('**', '*'),
            array('mytag', false),
            array('mytag *', false),
        );
    }
}
