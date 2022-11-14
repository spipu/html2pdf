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
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class Thead must not be empty
 */
class MustHaveTagsTest extends AbstractTest
{
    /**
     * test
     *
     * @return void
     */
    public function testOk()
    {
        $html = '<table>';
        $html.= '<thead><tr><td>Hello</td></tr></thead>';
        $html.= '<tbody><tr><td>World</td></tr></tbody>';
        $html.= '</table>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $result = $object->output('test.pdf', 'S');

        $this->assertNotEmpty($result);
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotEmptyThead()
    {
        $this->expectException(\Spipu\Html2Pdf\Exception\HtmlParsingException::class);
        $html = '<table>';
        $html.= '<thead></thead>';
        $html.= '<tbody><tr><td>World</td></tr></tbody>';
        $html.= '</table>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $object->output('test.pdf', 'S');
    }

    /**
     * test
     *
     * @return void
     */
    public function testNotEmptyTfoot()
    {
        $this->expectException(\Spipu\Html2Pdf\Exception\HtmlParsingException::class);
        $html = '<table>';
        $html.= '<tfoot></tfoot>';
        $html.= '<tbody><tr><td>World</td></tr></tbody>';
        $html.= '</table>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $object->output('test.pdf', 'S');
    }
}
