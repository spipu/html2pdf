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

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9;

use PHPUnit\Framework\TestCase;
use Spipu\Html2Pdf\Parsing\Html;

abstract class HtmlTestCase extends TestCase
{
    /**
     * @var Html
     */
    protected $object;

    protected function setUp(): void
    {
        $textParser = $this->getMockBuilder('Spipu\Html2Pdf\Parsing\TextParser')
            ->disableOriginalConstructor()
            ->setMethods(['prepareTxt'])
            ->getMock();

        $textParser
            ->expects($this->any())
            ->method('prepareTxt')
            ->willReturnCallback([$this, 'mockPrepareTxt']);

        $this->object = new Html($textParser);
    }
}
