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

use PHPUnit_Framework_TestCase;
use Spipu\Html2Pdf\Parsing\HtmlLexer;

/**
 * Class HtmlLexerTest
 */
class HtmlLexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test: tokenize
     *
     * @param string $html           html to test
     * @param array  $expectedTokens expected token
     *
     * @dataProvider tokenizeProvider
     */
    public function testTokenize($html, $expectedTokens)
    {
        $lexer = new HtmlLexer();
        $tokens = $lexer->tokenize($html);

        $this->assertEquals(count($expectedTokens), count($tokens));

        for ($i = 0; $i < count($tokens); $i++) {
            $this->assertEquals($expectedTokens[$i][0], $tokens[$i]->getType());
            $this->assertEquals($expectedTokens[$i][1], $tokens[$i]->getData());
            $this->assertEquals($expectedTokens[$i][2], $tokens[$i]->getLine());
        }
    }

    /**
     * provider: tokenize
     *
     * @return array
     */
    public function tokenizeProvider()
    {
        return array(
            array(
                '<p>test</p>',
                array(
                    array('code', '<p>', 1),
                    array('txt', 'test', -1),
                    array('code', '</p>', 1),
                )
            ),
            array(
                "<a><!-- comment -->\n<b><c>test",
                array(
                    array('code', '<a>', 1),
                    array('txt', "\n", -1),
                    array('code', '<b>', 2),
                    array('code', '<c>', 2),
                    array('txt', "test", -1),
                )
            )
        );
    }
}
