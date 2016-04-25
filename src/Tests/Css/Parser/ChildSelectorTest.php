<?php

namespace Spipu\Html2Pdf\Tests\Css\Parser;

use \Phake;
use Spipu\Html2Pdf\Css\Selector\ChildSelector;

/**
 * Class ChildSelectorTest
 */
class ChildSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $nodeChild = Phake::mock('Spipu\Html2Pdf\Html\NodeInterface');
        $nodeParent = Phake::mock('Spipu\Html2Pdf\Html\NodeInterface');
        Phake::when($nodeChild)->getParent()->thenReturn($nodeParent);

        $okSelector = Phake::mock('Spipu\Html2Pdf\Css\Selector\SelectorInterface');
        Phake::when($okSelector)->validate($nodeParent)->thenReturn($nodeParent);

        $koSelector = Phake::mock('Spipu\Html2Pdf\Css\Selector\SelectorInterface');
        Phake::when($koSelector)->validate($nodeParent)->thenReturn(false);

        $selector = new ChildSelector(' ');
        $selector->setPrevious($okSelector);
        $result = $selector->validate($nodeChild);
        $this->assertEquals($nodeParent, $result);

        $selector->setPrevious($koSelector);
        $result = $selector->validate($nodeChild);
        $this->assertFalse($result);
    }
}
