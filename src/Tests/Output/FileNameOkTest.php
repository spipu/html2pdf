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
 * Class FileNameOkTest
 */
class FileNameOkTest extends AbstractTest
{
    /**
     * test: the file extension must be PDF
     *
     * @return void
     */
    public function testCase()
    {
        $object = $this->getObject();
        $object->writeHTML('Hello World');
        $result = $object->output('test.pdf', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }
}
