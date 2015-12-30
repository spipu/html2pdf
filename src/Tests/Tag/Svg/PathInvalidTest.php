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
 * Class PathInvalidTest
 */
class PathInvalidTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testCase()
    {
        $html = '
<page>
    <draw style="width:150mm; height:100mm;">
        <path style="fill:#770000; stroke:#AA0033;" d="n 20mm,40mm a16mm,8mm 0,0,0 16mm,8mm" />
    </draw>
</page>';

        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML($html);
        $object->Output('test.pdf', 'S');
    }
}
