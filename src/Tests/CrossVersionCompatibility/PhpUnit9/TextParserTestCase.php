<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

use Spipu\Html2Pdf\Parsing\TextParser;

abstract class TextParserTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TextParser
     */
    protected $parser;

    protected function setUp(): void
    {
        $this->parser = new TextParser();
    }
}
