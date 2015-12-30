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

namespace Spipu\Html2Pdf\Tests\Tag\Svg;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class RectErrorTest
 */
class RectErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<rect />');
        $object->Output('test.pdf', 'S');
    }
}
