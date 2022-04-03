<?php

namespace Spipu\Html2Pdf\Tests;

/**
 * Class Html2PdfTest
 */
class Html2PdfTest extends AbstractTest
{
    public function testExtensionTag()
    {
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
    }

    /**
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     * @expectedExceptionMessage Unauthorized path scheme
     */
    public function testSecurityKo()
    {
        $object = $this->getObject();
        $object->writeHTML('<div><img src="phar://test.com/php.phar" alt="" /></div>');
    }
}
