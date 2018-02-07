<?php

namespace Spipu\Html2Pdf\Tests;

use Phake;
use Spipu\Html2Pdf\SvgDrawer;

/**
 * Class Html2PdfTest
 */
class SvgDrawerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SvgDrawer
     */
    private $svgDrawer;

    public function setUp()
    {
        $myPdf = Phake::mock('Spipu\Html2Pdf\MyPdf');

        $cssConverter = Phake::mock('Spipu\Html2Pdf\CssConverter');

        $this->svgDrawer = new SvgDrawer($myPdf, $cssConverter);
    }

    /**
     * Test IsDrawing Exception
     *
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testIsDrawingException()
    {
        $properties = [
            'x' => 0,
            'y' => 0,
            'w' => '100mm',
            'h' => '100mm',
        ];

        $this->svgDrawer->startDrawing($properties);
        $this->svgDrawer->startDrawing($properties);
    }

    /**
     * Test IsDrawing
     */
    public function testIsDrawingOk()
    {
        $properties = [
            'x' => 0,
            'y' => 0,
            'w' => '100mm',
            'h' => '100mm',
        ];

        $this->assertFalse($this->svgDrawer->isDrawing());
        $this->svgDrawer->startDrawing($properties);
        $this->assertTrue($this->svgDrawer->isDrawing());
        $this->svgDrawer->stopDrawing();
        $this->assertFalse($this->svgDrawer->isDrawing());
    }

    /**
     * Test properties
     */
    public function testProperties()
    {
        $properties = [
            'x' => '1mm',
            'y' => '2mm',
            'w' => '3mm',
            'h' => '4mm',
        ];

        $this->svgDrawer->startDrawing($properties);

        $this->assertSame('1mm', $this->svgDrawer->getProperty('x'));
        $this->assertSame('2mm', $this->svgDrawer->getProperty('y'));
        $this->assertSame('3mm', $this->svgDrawer->getProperty('w'));
        $this->assertSame('4mm', $this->svgDrawer->getProperty('h'));
    }
}
