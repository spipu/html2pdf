<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class BackgroundOkTest
 */
class BackgroundOkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: The image src is unknown
     *
     * @return void
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<div style="background-image: url('.dirname(__FILE__).'/res/logo.png)">Hello World</div>');
        $result = $object->Output('test.pdf', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }
}
