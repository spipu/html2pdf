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

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class SrcOkTest
 */
class SrcOkTest extends \PHPUnit_Framework_TestCase
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
        $object->writeHTML('Hello World <img src="'.dirname(__FILE__).'/res/logo.png" />');
        $result = $object->output('test.pdf', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }
}
