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

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Tests\CrossVersionCompatibility\TextParserTestCase;

/**
 * Class TextParserTest
 */
class TextParserTest extends TextParserTestCase
{
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
