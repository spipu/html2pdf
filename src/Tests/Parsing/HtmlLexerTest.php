<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Parsing\HtmlLexer;
use Spipu\Html2Pdf\Parsing\Token;

/**
 * Class HtmlLexerTest
 */
class HtmlLexerTest extends \PHPUnit_Framework_TestCase
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
        $tokenStream = $lexer->tokenize($html);

        $this->assertEquals(count($expectedTokens), $tokenStream->count());

        $i = 0;
        while ($tokenStream->current() !== null) {
            $this->assertEquals($expectedTokens[$i][0], $tokenStream->current()->getType());
            $this->assertEquals($expectedTokens[$i][1], $tokenStream->current()->getData());
            $this->assertEquals($expectedTokens[$i][2], $tokenStream->current()->getLine());
            $i++;
            $tokenStream->next();
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
                    array(Token::TAG_OPEN_TYPE, '<p>', 1),
                    array(Token::TEXT_TYPE, 'test', -1),
                    array(Token::TAG_CLOSE_TYPE, '</p>', 1),
                )
            ),
            array(
                "<a><!-- comment -->\n<b><c>",
                array(
                    array(Token::TAG_OPEN_TYPE, '<a>', 1),
                    array(Token::TEXT_TYPE, "\n", -1),
                    array(Token::TAG_OPEN_TYPE, '<b>', 2),
                    array(Token::TAG_OPEN_TYPE, '<c>', 2),
                )
            )
        );
    }
}
