<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\CssConverter;

/**
 * Class CssConverterTest
 */
class CssConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CssConverter
     */
    private $cssConverter;

    public function setUp()
    {
        $this->cssConverter = new CssConverter();
    }

    /**
     * @param string $css
     * @param string $old
     * @param array  $expected
     *
     * @dataProvider convertToMMProvider
     */
    public function testConvertToMM($css, $old, $expected)
    {
        $result = $this->cssConverter->convertToMM($css, $old);

        $this->assertEquals($expected, $result);
    }

    public function convertToMMProvider()
    {
        return array(
            array('100mm', null, 100),
            array('100px', null, 25.4 / 96. * 100),
            array('100',   null, 25.4 / 96. * 100),
            array('100pt', null, 25.4 / 72. * 100),
            array('100in', null, 25.4 * 100),
            array('10%',    100, 10),
            array('100cm', null, null),
        );
    }

    /**
     * @param string $css
     * @param array  $expected
     *
     * @dataProvider convertToRadiusProvider
     */
    public function testConvertToRadius($css, $expected)
    {
        $result = $this->cssConverter->convertToRadius($css);

        $this->assertEquals(count($expected), count($result));

        for ($i = 0; $i < count($result); $i++) {
            $this->assertEquals($expected[$i], $result[$i]);
        }
    }

    public function convertToRadiusProvider()
    {
        return array(
            array('100mm',           array(100)),
            array('100mm 10mm',      array(100, 10)),
            array('100mm  10mm ',    array(100, 10)),
            array('100mm 10cm 10mm', array(100, 10)),
            array('1mm 2mm 3mm 4mm', array(1, 2, 3, 4)),
        );
    }

    /**
     * @param string  $css
     * @param boolean $expectedRes
     * @param array   $expectedColor
     *
     * @dataProvider convertToColorProvider
     */
    public function testConvertToColor($css, $expectedRes, $expectedColor)
    {
        $res = true;
        $resultColor = $this->cssConverter->convertToColor($css, $res);

        $this->assertEquals($expectedRes, $res);
        $this->assertEquals(count($expectedColor), count($resultColor));

        for ($i = 0; $i < count($resultColor); $i++) {
            if (is_null($expectedColor[$i])) {
                $this->assertNull($resultColor[$i]);
            } else {
                $this->assertEquals($expectedColor[$i], $resultColor[$i]);
            }
        }
    }

    public function convertToColorProvider()
    {
        return array(
            array('transparent',              true,  array(null, null, null)),
            array('aliceblue',                true,  array( 240,  248,  255)),
            array('#F0A050',                  true,  array( 240,  160,   80)),
            array('#FA5',                     true,  array( 255,  170,   85)),
            array('rgb(  50, 100, 150)',      true,  array(  50,  100,  150)),
            array('rgb( 10%, 20%, 30%)',      true,  array(25.5,   51, 76.5)),
            array('rgb( 0.2, 0.4, 0.6)',      true,  array(  51,  102,  153)),
            array('cmyk(255, 255, 255, 255)', true,  array( 100,  100,  100, 100)),
            array('cmyk(10%, 20%, 30%, 40%)', true,  array(  10,   20,   30,  40)),
            array('cmyk(0.2, 0.4, 0.6, 0.8)', true,  array(  20,   40,   60,  80)),
            array('blakc',                    false, array(   0,    0,    0)),
        );
    }
}
