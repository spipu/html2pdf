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
use Spipu\Html2Pdf\Parsing\TextParser;

abstract class TextParserTestCase extends TestCase
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
