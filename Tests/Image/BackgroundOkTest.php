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

namespace Spipu\Html2Pdf\Tests\Image;

use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class BackgroundOkTest
 */
class BackgroundOkTest extends AbstractTest
{
    /**
     * test: The image src is unknown
     *
     * @return void
     */
    public function testCase()
    {
        $object = $this->getObject();
        $object->writeHTML('<div style="background-image: url('.dirname(__FILE__).'/res/logo.png)">Hello World</div>');
        $result = $object->output('test.pdf', 'S');

        $this->assertContains('PhpUnit Test', $result);
    }
}
