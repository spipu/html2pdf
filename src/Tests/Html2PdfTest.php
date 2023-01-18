<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\Exception\HtmlParsingException;

/**
 * Class Html2PdfTest
 */
class Html2PdfTest extends AbstractTest
{
    public function testExtensionTag()
    {
        /**
         * Ignore deprecation errors as this causes this test to fail on deprecated toString.
         * Later versions of phpunit allow this to be set in the XML file, but this isn't
         * available until phpunit v6.2.
         */
        error_reporting(E_ALL & ~E_DEPRECATED);

        $tag = $this->createMock('Spipu\Html2Pdf\Tag\TagInterface');
        $tag->expects($this->any())->method('getName')->willReturn('test_tag');
        $tag->expects($this->exactly(4))->method('open');
        $tag->expects($this->exactly(2))->method('close');

        $extension = $this->createMock('Spipu\Html2Pdf\Extension\ExtensionInterface');
        $extension->expects($this->any())->method('getName')->willReturn('test');
        $extension->expects($this->any())->method('getTags')->willReturn(array($tag));

        $object = $this->getObject();

        $object->addExtension($extension);
        $object->writeHTML('<div><test_tag>Hello</test_tag></div>');
    }

    public function testSecurityGood()
    {
        $object = $this->getObject();
        $object->setTestIsImage(false);
        $object->writeHTML('<div><img src="https://www.spipu.net/res/logo_spipu.gif" alt="" /></div>');
        $object->writeHTML('<div><img src="/temp/test.jpg" alt="" /></div>');
        $object->writeHTML('<div><img src="c:/temp/test.jpg" alt="" /></div>');
        $this->assertTrue(true);
    }

    public function testSecurityKo()
    {
        $this->expectException(HtmlParsingException::class);
        $this->expectExceptionMessage("Unauthorized path scheme");
        $object = $this->getObject();
        $object->writeHTML('<div><img src="phar://test.com/php.phar" alt="" /></div>');
    }
}
