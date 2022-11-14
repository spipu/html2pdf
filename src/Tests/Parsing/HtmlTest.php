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

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    require_once __DIR__ . '/../CrossVersionCompatibility/PhpUnit9/HtmlTestCase.php';
} else {
    require_once __DIR__ . '/../CrossVersionCompatibility/PhpUnit5/HtmlTestCase.php';
}

/**
 * Class HtmlTest
 */
class HtmlTest extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\HtmlTestCase
{
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
