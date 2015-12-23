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
use Spipu\Html2Pdf\Tag\AbstractDefaultTag;

/**
 * Class TagInterfaceOkTest
 */
class TagInterfaceOkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: The tag class must implement TagInterface
     *
     * @return void
     */
    public function testCase()
    {
        $object = new Html2Pdf();
        $result = $object->addTagDefinition('Spipu\\Html2Pdf\\Tests\\Tag\\TagExampleOK');

        $this->assertEquals(true, $result);
    }
}

/**
 * Test Class TagExampleOK
 */
class TagExampleOK extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'example';
    }
}
