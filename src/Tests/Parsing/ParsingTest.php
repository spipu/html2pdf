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
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class ParsingTest
 */
class ParsingTest extends AbstractTest
{
    /**
     * test: The tag is unknown
     *
     * @return            void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testUnknownTag()
    {
        $object = $this->getObject();
        $object->writeHTML('<bad_tag>Hello World</bad_tag>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: Too many tag closures found
     *
     * @return            void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testTooManyClosuresFound()
    {
        $object = $this->getObject();
        $object->writeHTML('<i><u>Hello</u></i></b>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: Tags are closed in a wrong order
     *
     * @return            void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testWrongClosedOrder()
    {
        $object = $this->getObject();
        $object->writeHTML('<b><u><i>Hello</u></i></b>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: The following tag has not been closed
     *
     * @return            void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testNotClosed()
    {
        $object = $this->getObject();
        $object->writeHTML('<b><i>Hello</i>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: The following tags have not been closed
     *
     * @return            void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testNotClosedMore()
    {
        $object = $this->getObject();
        $object->writeHTML('<b><u><i>Hello</i>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: The HTML tag code provided is invalid
     *
     * @return            void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testInvalidCode()
    {
        $object = $this->getObject();
        $object->writeHTML('<az1-r_h>Hello</az1-r_h>');
        $object->output('test.pdf', 'S');
    }
}
