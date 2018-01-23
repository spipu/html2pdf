<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Parsing\Html;

/**
 * Class HtmlTest
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Html
     */
    private $object;

    protected function setUp()
    {
        $textParser = $this->getMockBuilder('Spipu\Html2Pdf\Parsing\TextParser')
            ->disableOriginalConstructor()
            ->setMethods(['prepareTxt'])
            ->getMock();

        $textParser
            ->expects($this->any())
            ->method('prepareTxt')
            ->will($this->returnCallback([$this, 'mockPrepareTxt']));

        $this->object = new Html($textParser);
    }

    /**
     * mock of prepareTxt method
     *
     * @param $txt
     * @param bool $spaces
     * @return mixed
     */
    public function mockPrepareTxt($txt, $spaces = true)
    {
        return $txt;
    }

    /**
     * Test the prepareHtml method
     */
    public function testPrepareHtml()
    {
        $result = $this->object->prepareHtml('Hello [[date_y]]-[[date_m]]-[[date_d]] World');
        $this->assertSame('Hello '.date('Y-m-d').' World', $result);

        $result = $this->object->prepareHtml('Hello [[date_h]]:[[date_i]]:[[date_s]] World');
        $this->assertSame('Hello '.date('H:i:s').' World', $result);

        $html  = '
<html>
    <head>
        <style type="text">.my-class { color: red; }</style>
        <link type="text/css" href="my-style.css"/>
    </head>
    <body class="my-class"><p>Hello World</p></body>
</html>';

        $expected='<style type="text">.my-class { color: red; }</style>'.
            '<link type="text/css" href="my-style.css"/>'.
            '<page class="my-class"><p>Hello World</p></page>';

        $result = $this->object->prepareHtml($html);
        $this->assertSame($expected, $result);
    }
}
