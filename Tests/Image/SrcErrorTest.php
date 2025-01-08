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

use Spipu\Html2Pdf\Exception\ImageException;
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class SrcErrorTest
 */
class SrcErrorTest extends AbstractTest
{
    /**
     * test: The image src is unknown
     *
     * @return void
     */
    public function testCase()
    {
        $this->expectException(ImageException::class);
        $image = '/res/wrong.png';

        try {
            $object = $this->getObject();
            $object->writeHTML('Hello World <img src="'.$image.'" />');
            $object->output('test.pdf', 'S');
        } catch (ImageException $e) {
            $this->assertSame($image, $e->getImage());
            throw $e;
        }
    }
}
