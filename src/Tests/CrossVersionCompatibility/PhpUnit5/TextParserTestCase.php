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

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5;

use PHPUnit_Framework_TestCase;
use Spipu\Html2Pdf\Parsing\TextParser;

abstract class TextParserTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var TextParser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = new TextParser();
    }
}
