<?php 

namespace Spipu\Html2Pdf\Tests\Css\Parser;

use \Phake;
use Spipu\Html2Pdf\Css\Parser\RuleParser;

/**
 * Class RuleParserTest
 */
class RuleParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $selector1 = Phake::mock('Spipu\Html2Pdf\Css\Selector\SelectorInterface');
        Phake::when($selector1)->getName()->thenReturn('s_a');
        Phake::when($selector1)->getText()->thenReturn('a');
        $parser1 = Phake::mock('Spipu\Html2Pdf\Css\Parser\SelectorParserInterface');
        Phake::when($parser1)->match('abc')->thenReturn($selector1);

        $selector2 = Phake::mock('Spipu\Html2Pdf\Css\Selector\SelectorInterface');
        Phake::when($selector2)->getName()->thenReturn('s_b');
        Phake::when($selector2)->getText()->thenReturn('b');
        $parser2 = Phake::mock('Spipu\Html2Pdf\Css\Parser\SelectorParserInterface');
        Phake::when($parser2)->match('bc')->thenReturn($selector2);

        $selector3 = Phake::mock('Spipu\Html2Pdf\Css\Selector\SelectorInterface');
        Phake::when($selector3)->getName()->thenReturn('s_c');
        Phake::when($selector3)->getText()->thenReturn('c');
        $parser3 = Phake::mock('Spipu\Html2Pdf\Css\Parser\SelectorParserInterface');
        Phake::when($parser3)->match('c')->thenReturn($selector3);

        $parsers = array($parser1, $parser2, $parser3);
        $selectorProvider = Phake::mock('Spipu\Html2Pdf\Css\SelectorProvider');
        Phake::when($selectorProvider)->getParsers()->thenReturn($parsers);

        $ruleParser = new RuleParser();
        $selectors = $ruleParser->parse($selectorProvider, 'abc');

        $expected = array(
            array('s_a', 'a'), array('s_b', 'b'), array('s_c', 'c')
        );
        $i = 0;
        foreach ($selectors as $selector) {
            $this->assertEquals($expected[$i][0], $selector->getName());
            $this->assertEquals($expected[$i][1], $selector->getText());
            $i++;
        }
        $this->assertEquals(count($expected), $i);

        $this->setExpectedException('Exception');
        $ruleParser->parse($selectorProvider, '.test super');
    }
}
