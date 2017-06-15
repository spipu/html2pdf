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

namespace Spipu\Html2Pdf\Tests\Tag;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class FileNameErrorTest
 */
class FileNameErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: the file extension must be PDF
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<p>Hello World</p>');
        $object->output('test.bad', 'S');
    }
}
