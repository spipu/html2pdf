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

use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class GErrorTest
 */
class GErrorTest extends AbstractTest
{
    /**
     * test
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     */
    public function testCase()
    {
        $object = $this->getObject();
        $object->writeHTML('<g />');
        $object->output('test.pdf', 'S');
    }
}
