<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\Tag\AbstractTag;

/**
 * Class Html2PdfTest
 */
class Html2PdfTest extends AbstractTest
{
    public function testExtensionTag()
    {
        $tag = new testTag();

        $extension = $this->createMock('Spipu\Html2Pdf\Extension\ExtensionInterface');
        $extension->expects($this->any())->method('getName')->willReturn('test');
        $extension->expects($this->any())->method('getTags')->willReturn(array($tag));

        $object = $this->getObject();

        $object->addExtension($extension);
        $object->writeHTML('<div><test_tag>Hello</test_tag></div>');

        $this->assertTrue(true);
    }

    public function testSecurityGood()
    {
        $object = $this->getObject();
        $object->setTestIsImage(false);
        $object->writeHTML('<div><img src="https://www.spipu.net/res/logo_spipu.gif" alt="" /></div>');
        $object->writeHTML('<div><img src="/temp/test.jpg" alt="" /></div>');
        $object->writeHTML('<div><img src="c:/temp/test.jpg" alt="" /></div>');

        // Ensures we assert something
        $this->assertTrue(true);
    }

    public function testSecurityKo()
    {
        $this->expectException(\Spipu\Html2Pdf\Exception\HtmlParsingException::class);
        $this->expectExceptionMessage('Unauthorized path scheme', \Spipu\Html2Pdf\Exception\HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<div><img src="phar://test.com/php.phar" alt="" /></div>');
    }
}

class testTag extends AbstractTag
{
    public function getName()
    {
        return 'test_tag';
    }

    public function open($properties)
    {
    }

    public function close($properties)
    {
    }
}
