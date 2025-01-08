<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Image;

use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class SrcOkTest
 */
class SrcOkTest extends AbstractTest
{
    /**
     * test: The image src is unknown
     *
     * @return void
     */
    public function testCase()
    {
        $object = $this->getObject();
        $object->writeHTML('Hello World <img src="'.dirname(__FILE__).'/res/logo.png" />');
        $result = $object->output('test.pdf', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }
}
