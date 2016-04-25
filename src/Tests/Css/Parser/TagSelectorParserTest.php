<?php

namespace Spipu\Html2Pdf\Tests\Css\Parser;

use Spipu\Html2Pdf\Css\Parser\TagSelectorParser;

/**
 * Class TagSelectorParserTest
 */
class TagSelectorParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $text
     * @param $matchedText
     *
     * @dataProvider matchProvider
     */
    public function testMatch($text, $matchedText)
    {
        $selector = new TagSelectorParser();
        $result = $selector->match($text);

        if ($matchedText == false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('Spipu\Html2Pdf\Css\Selector\TagSelector', $result);
            $this->assertEquals($matchedText, $result->getText());
        }
    }

    public function matchProvider()
    {
        return array(
            array('#myid', false),
            array('#myid mytag', false),
            array('mytag', 'mytag'),
            array('mytag mytag', 'mytag'),
        );
    }
}
