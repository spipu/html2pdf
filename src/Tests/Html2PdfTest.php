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

    /**
     * @expectedException \Spipu\Html2Pdf\Exception\HtmlParsingException
     * @expectedExceptionMessage Unauthorized path scheme
     */
    public function testSecurity()
    {
        $object = $this->getObject();

        $object->writeHTML('<div><img src="phar://test.com/php.phar" alt="" /></div>');
    }
}
