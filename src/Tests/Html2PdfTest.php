<?php

namespace Spipu\Html2Pdf\Tests;

use Phake;
use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class Html2PdfTest
 */
class Html2PdfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Html2Pdf
     */
    private $html2pdf;

    public function setUp()
    {
        $this->html2pdf = new Html2Pdf();
    }

    public function testExtensionTag()
    {
        $tag = Phake::mock('Spipu\Html2Pdf\Tag\TagInterface');
        Phake::when($tag)->getName()->thenReturn('test_tag');

        $extension = Phake::mock('Spipu\Html2Pdf\Extension\ExtensionInterface');
        Phake::when($extension)->getName()->thenReturn('test');
        Phake::when($extension)->getTags()->thenReturn(array($tag));
        $this->html2pdf->addExtension($extension);

        $this->html2pdf->writeHTML('<test_tag/>');

        Phake::verify($tag)->open;
        Phake::verify($tag, Phake::times(2))->close; // TODO Html2Pdf should probably call this only one time
    }
}
