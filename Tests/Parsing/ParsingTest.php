<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class ParsingTest
 */
class ParsingTest extends AbstractTest
{
    /**
     * test: The tag is unknown
     *
     * @return void
     */
    public function testUnknownTag()
    {
        $this->expectException(HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<bad_tag>Hello World</bad_tag>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: Too many tag closures found
     *
     * @return void
     */
    public function testTooManyClosuresFound()
    {
        $this->expectException(HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<i><u>Hello</u></i></b>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: Tags are closed in a wrong order
     *
     * @return void
     */
    public function testWrongClosedOrder()
    {
        $this->expectException(HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<b><u><i>Hello</u></i></b>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: The following tag has not been closed
     *
     * @return void
     */
    public function testNotClosed()
    {
        $this->expectException(HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<b><i>Hello</i>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: The following tags have not been closed
     *
     * @return void
     */
    public function testNotClosedMore()
    {
        $this->expectException(HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<b><u><i>Hello</i>');
        $object->output('test.pdf', 'S');
    }

    /**
     * test: The HTML tag code provided is invalid
     *
     * @return void
     */
    public function testInvalidCode()
    {
        $this->expectException(HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<az1-r_h>Hello</az1-r_h>');
        $object->output('test.pdf', 'S');
    }
}
