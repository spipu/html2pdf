<?php

namespace Spipu\Html2Pdf\Tests\Css\Parser;

use Spipu\Html2Pdf\Css\Parser\PseudoSelectorParser;

/**
 * Class PseudoSelectorParserTest
 */
class PseudoSelectorParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $text
     * @param $matchedText
     *
     * @dataProvider matchProvider
     */
    public function testMatch($text, $matchedText)
    {
        $selector = new PseudoSelectorParser();
        $result = $selector->match($text);

        if ($matchedText == false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('Spipu\Html2Pdf\Css\Selector\PseudoSelector', $result);
            $this->assertEquals($matchedText, $result->getText());
        }
    }

    public function matchProvider()
    {
        return array(
            array(':after', ':after'),
            array(':first-child', ':first-child'),
            array(':nth-child(2)', ':nth-child(2)'),
            array(':nth-child(2n + 5)', ':nth-child(2n + 5)'),
            array(':nth-child(2n', ':nth-child'),
            array(':', false),
            array('.myclass:before', false),
            array('.myclass :before', false),
        );
    }
}
