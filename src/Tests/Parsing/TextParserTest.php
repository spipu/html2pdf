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

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Parsing\TextParser;

/**
 * Class TextParserTest
 */
class TextParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TextParser
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = new TextParser();
    }

    /**
     * Test if it works
     */
    public function testOk()
    {
        $result = $this->parser->prepareTxt('hello  world', false);
        $this->assertSame('hello  world', $result);

        $result = $this->parser->prepareTxt('hello  world', true);
        $this->assertSame('hello world', $result);

        $result = $this->parser->prepareTxt('hello 10&euro; world');
        $this->assertSame('hello 10€ world', $result);

        $result = $this->parser->prepareTxt('hello &lt; world');
        $this->assertSame('hello < world', $result);
    }
}
