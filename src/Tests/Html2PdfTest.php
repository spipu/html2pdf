<?php

namespace Spipu\Html2Pdf\Tests;

use Phake;
use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class Html2PdfTest
 */
class Html2PdfTest extends AbstractTest
{
    public function testExtensionTag()
    {
        $tag = Phake::mock('Spipu\Html2Pdf\Tag\TagInterface');
        Phake::when($tag)->getName()->thenReturn('test_tag');

        $extension = Phake::mock('Spipu\Html2Pdf\Extension\ExtensionInterface');
        Phake::when($extension)->getName()->thenReturn('test');
        Phake::when($extension)->getTags()->thenReturn(array($tag));

        $object = $this->getObject();

        $object->addExtension($extension);
        $object->writeHTML('<div><test_tag>Hello</test_tag></div>');

        Phake::verify($tag, Phake::times(4))->open;
        Phake::verify($tag, Phake::times(2))->close;
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
