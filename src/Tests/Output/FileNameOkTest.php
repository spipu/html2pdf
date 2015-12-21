<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Tag;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class TagInterfaceOkTest
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
 */
class FileNameOkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: the file extension must be PDF
     *
     * @return void
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->writeHTML('Hello World');
        $object->pdf->SetTitle('PhpUnit Test');
        $result = $object->Output('test.pdf', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }
}
