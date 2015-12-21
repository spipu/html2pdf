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

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class UnknownTagTest
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
 */
class UnknownTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: The tag is unknown
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->writeHTML('<bad_tag>Hello World</bad_tag>');
        $object->Output('test.pdf', 'S');
    }
}
