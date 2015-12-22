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

namespace Spipu\Html2Pdf\Tests\Tag\Svg;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class GErrorTest
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
 */
class GErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: The tag class must implement TagInterface
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<g />');
        $object->Output('test.pdf', 'S');
    }
}
