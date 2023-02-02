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

namespace Spipu\Html2Pdf\Tests\Tag\Svg;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class PathInvalidTest
 */
class PathInvalidTest extends AbstractTest
{
    /**
     * test
     *
     * @return void
     */
    public function testCase()
    {
        $this->expectException(HtmlParsingException::class);
        $html = '
<page>
    <draw style="width:150mm; height:100mm;">
        <path style="fill:#770000; stroke:#AA0033;" d="n 20mm,40mm a16mm,8mm 0,0,0 16mm,8mm" />
    </draw>
</page>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $object->output('test.pdf', 'S');
    }
}
