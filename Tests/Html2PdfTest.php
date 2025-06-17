<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
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

    public function testSecurityGoodImg()
    {
        $object = $this->getObject();
        $object->getSecurityService()->addAllowedHost('www.spipu.net');
        $object->setTestIsImage(false);
        $object->writeHTML('<div><img src="https://www.spipu.net/res/logo_spipu.gif" alt="" /></div>');
        $object->writeHTML('<div><img src="/temp/test.jpg" alt="" /></div>');
        $object->writeHTML('<div><img src="c:/temp/test.jpg" alt="" /></div>');

        // Ensures we assert something
        $this->assertTrue(true);
    }

    public function testSecurityGoodBackground()
    {
        $object = $this->getObject();
        $object->getSecurityService()->addAllowedHost('www.spipu.net');
        $object->setTestIsImage(false);
        $object->writeHTML('<div><div style="background-image: url(https://www.spipu.net/res/logo_spipu.gif)" /></div>');
        $object->writeHTML('<div><div style="background-image: url(/temp/test.jpg)" /></div>');
        $object->writeHTML('<div><div style="background-image: url(c:/temp/test.jpg)" /></div>');

        // Ensures we assert something
        $this->assertTrue(true);
    }

    public function testSecurityKoImg()
    {
        $this->expectException(HtmlParsingException::class);
        $this->expectExceptionMessage('Unauthorized path scheme', HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<div><img src="phar://test.com/php.phar" alt="" /></div>');
    }

    public function testSecurityKoBackground()
    {
        $this->expectException(HtmlParsingException::class);
        $this->expectExceptionMessage('Unauthorized path scheme', HtmlParsingException::class);
        $object = $this->getObject();
        $object->writeHTML('<div><div style="background-image: url(phar://test.com/php.phar)" /></div>');
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
