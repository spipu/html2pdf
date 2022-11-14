<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

use Spipu\Html2Pdf\CssConverter;
use Spipu\Html2Pdf\SvgDrawer;

abstract class SvgDrawerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SvgDrawer
     */
    protected $svgDrawer;

    public function setUp()
    {
        $myPdf = $this->createMock('Spipu\Html2Pdf\MyPdf');

        $cssConverter = new CssConverter();

        $this->svgDrawer = new SvgDrawer($myPdf, $cssConverter);
    }
}
