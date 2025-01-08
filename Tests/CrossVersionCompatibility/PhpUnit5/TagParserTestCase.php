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

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5;

use PHPUnit_Framework_TestCase;
use Spipu\Html2Pdf\Parsing\TagParser;

abstract class TagParserTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var TagParser
     */
    protected $parser;

    protected function setUp()
    {
        $textParser = $this->getMockBuilder('Spipu\Html2Pdf\Parsing\TextParser')
            ->disableOriginalConstructor()
            ->setMethods(['prepareTxt'])
            ->getMock();

        $textParser
            ->expects($this->any())
            ->method('prepareTxt')
            ->will($this->returnCallback([$this, 'mockPrepareTxt']));

        $this->parser = new TagParser($textParser);
    }
}
