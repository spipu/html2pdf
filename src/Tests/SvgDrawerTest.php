<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\CssConverter;
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
        $myPdf = $this->createMock('Spipu\Html2Pdf\MyPdf');

        $cssConverter = new CssConverter();

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
            'w' => 100,
            'h' => 100,
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
            'x' => 1,
            'y' => 2,
            'w' => 3,
            'h' => 4,
        ];

        $this->svgDrawer->startDrawing($properties);

        $this->assertSame(1, $this->svgDrawer->getProperty('x'));
        $this->assertSame(2, $this->svgDrawer->getProperty('y'));
        $this->assertSame(3, $this->svgDrawer->getProperty('w'));
        $this->assertSame(4, $this->svgDrawer->getProperty('h'));
    }

    /**
     * Test: tokenize
     *
     * @param mixed $transform
     * @param mixed  $expected
     *
     * @dataProvider transformProvider
     */
    public function testTransform($transform, $expected)
    {
        $properties = [
            'x' => 0,
            'y' => 0,
            'w' => 100,
            'h' => 100,
        ];

        $this->svgDrawer->startDrawing($properties);

        $result = $this->svgDrawer->prepareTransform($transform);

        $this->assertArraySame($expected, $result);
    }

    /**
     * @param array $expected
     * @param array $result
     */
    protected function assertArraySame($expected, $result)
    {
        if (is_array($expected)) {
            foreach ($expected as $key => $value) {
                $expected[$key] = round($value, 5);
            }
        }

        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = round($value, 5);
            }
        }

        $this->assertSame($expected, $result);
    }

    /**
     * provider: tokenize
     *
     * @return array
     */
    public function transformProvider()
    {
        return array(
            array(
                false,
                null
            ),
            array(
                'no instruction',
                null
            ),
            array(
                'foo(1,2)',
                null
            ),
            array(
                'before scale( 0.1 , 0.2 ) after',
                [
                    0.1,  0.,
                    0.,   0.2,
                    0.,   0.
                ]
            ),
            array(
                'scale(0.1,0.2)',
                [
                    0.1, 0.,
                    0.,  0.2,
                    0.,  0.
                ]
            ),
            array(
                'scale(0.1)',
                [
                    0.1, 0.,
                    0.,  0.1,
                    0.,  0.
                ]
            ),
            array(
                'scale(,)',
                [
                    1., 0.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'scale()',
                [
                    1., 0.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'translate()',
                [
                    1., 0.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'translate(10mm)',
                [
                    1.,  0.,
                    0.,  1.,
                    10., 0.
                ]
            ),
            array(
                'translate(10mm, 20mm)',
                [
                    1.,  0.,
                    0.,  1.,
                    10., 20.
                ]
            ),
            array(
                'rotate()',
                [
                    1., 0.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'rotate(90)',
                [
                    0.,  1.,
                    -1., 0.,
                    0., 0.
                ]
            ),
            array(
                'rotate(180)',
                [
                    -1.,  0.,
                    0.,  -1.,
                    0.,   0.
                ]
            ),
            array(
                'rotate(180, 10mm, 10mm)',
                [
                    -1.,    0.,
                    0.,    -1.,
                    -20.,  -20.
                ]
            ),
            array(
                'skewx()',
                [
                    1., 0.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'skewx(45)',
                [
                    1., 0.,
                    1., 1.,
                    0., 0.
                ]
            ),
            array(
                'skewy()',
                [
                    1., 0.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'skewy(45)',
                [
                    1., 1.,
                    0., 1.,
                    0., 0.
                ]
            ),
            array(
                'matrix()',
                [
                    0., 0.,
                    0., 0.,
                    0., 0.
                ]
            ),
            array(
                'matrix(1,2,3,4,5%,6%)',
                [
                    1., 2.,
                    3., 4.,
                    5., 6.
                ]
            ),
        );
    }
}
