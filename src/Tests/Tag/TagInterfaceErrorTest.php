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

namespace Spipu\Html2Pdf\Tests\Tag;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class TagInterfaceErrorTest
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
 */
class TagInterfaceErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: The tag class must implement TagInterface
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $object->addTagDefinition('Spipu\Html2Pdf\Tests\Tag\TagExampleError');
    }
}

/**
 * Test Class TagExampleError
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
 */
class TagExampleError
{

}
