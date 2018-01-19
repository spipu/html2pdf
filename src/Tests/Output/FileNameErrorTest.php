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
 * Class FileNameErrorTest
 */
class FileNameErrorTest extends AbstractTest
{
    /**
     * test: the file extension must be PDF
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    public function testCase()
    {
        $object = $this->getObject();
        $object->writeHTML('<p>Hello World</p>');
        $object->output('test.bad', 'S');
    }
}
