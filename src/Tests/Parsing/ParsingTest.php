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
 * Class ParsingTest
 */
class ParsingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: The tag is unknown
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testUnknownTag()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<bad_tag>Hello World</bad_tag>');
        $object->Output('test.pdf', 'S');
    }

    /**
     * test: Too many tag closures found
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testTooManyClosuresFound()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<i><u>Hello</u></i></b>');
        $object->Output('test.pdf', 'S');
    }

    /**
     * test: Tags are closed in a wrong order
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testWrongClosedOrder()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<b><u><i>Hello</u></i></b>');
        $object->Output('test.pdf', 'S');
    }

    /**
     * test: The following tag has not been closed
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testNotClosed()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<b><i>Hello</i>');
        $object->Output('test.pdf', 'S');
    }

    /**
     * test: The HTML tag code provided is invalid
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testInvalidCode()
    {
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');
        $object->writeHTML('<az1-r_h>Hello</az1-r_h>');
        $object->Output('test.pdf', 'S');
    }
}
