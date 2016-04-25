<?php

namespace Spipu\Html2Pdf\Tests\Css;

use \Phake;
use Spipu\Html2Pdf\Css\Rule;

/**
 * Class RuleTest
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $selectorsResult
     * @param bool  $expectedResult
     *
     * @dataProvider matchProvider
     */
    public function testMatch($selectorsResult, $expectedResult)
    {
        $node = Phake::mock('Spipu\Html2Pdf\Html\NodeInterface');
        $selectors = array();
        foreach ($selectorsResult as $selectorResult) {
            $selector = Phake::mock('Spipu\Html2Pdf\Css\Selector\SelectorInterface');
            $returnValue = $selectorResult ? $node : false;
            Phake::when($selector)->validate->thenReturn($returnValue);
            $selectors[] = $selector;
        }

        $node = Phake::mock('Spipu\Html2Pdf\Html\NodeInterface');
        $rule = new Rule('', $selectors);
        $result = $rule->match($node);
        $this->assertEquals($expectedResult, $result);
    }

    public function matchProvider()
    {
        return array(
            array(array(true), true),
            array(array(true, true), true),
            array(array(true, false), false),
            array(array(false, true), false),
            array(array(false, false), false),
        );
    }
}
