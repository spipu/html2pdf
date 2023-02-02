<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9;

use PHPUnit\Framework\TestCase;
use Spipu\Html2Pdf\CssConverter;
use Spipu\Html2Pdf\SvgDrawer;

abstract class SvgDrawerTestCase extends TestCase
{
    /**
     * @var SvgDrawer
     */
    protected $svgDrawer;

    public function setUp(): void
    {
        $myPdf = $this->createMock('Spipu\Html2Pdf\MyPdf');

        $cssConverter = new CssConverter();

        $this->svgDrawer = new SvgDrawer($myPdf, $cssConverter);
    }
}
