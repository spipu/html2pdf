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

namespace Spipu\Html2Pdf\Tests\Output;

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class FileNameOkTest
 */
class FileNameOkTest extends AbstractTest
{
    /**
     * test: the file extension must be PDF - OK
     *
     * @return void
     */
    public function testOk()
    {
        $object = $this->getObject();
        $object->writeHTML('Hello World');

        ob_start();
        $object->output('test.pdf');
        $result = ob_get_clean();

        $this->assertContains('PhpUnit Test', $result);
    }

    /**
     * test: the file extension is ignored if output string
     *
     * @return void
     */
    public function testIgnore()
    {
        $object = $this->getObject();
        $object->writeHTML('Hello World');
        $result = $object->output('test.bad', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }

    /**
     * test: the file extension must be PDF - Error
     *
     * @return void
     */
    public function testError()
    {
        $this->expectException(Html2PdfException::class);
        $object = $this->getObject();
        $object->writeHTML('<p>Hello World</p>');
        $object->output('test.bad');
    }
}
