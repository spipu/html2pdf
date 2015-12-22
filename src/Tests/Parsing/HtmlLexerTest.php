<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Parsing\HtmlLexer;

/**
 * Class HtmlLexerTest
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
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
        $tokens = $lexer->tokenize($html);

        $this->assertEquals(count($expectedTokens), count($tokens));

        for ($i = 0; $i < count($tokens); $i++) {
            $this->assertEquals($expectedTokens[$i][0], $tokens[$i]->getType());
            $this->assertEquals($expectedTokens[$i][1], $tokens[$i]->getData());
            $this->assertEquals($expectedTokens[$i][2], $tokens[$i]->getOffset());
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
                    array('code', '<p>', 0),
                    array('txt', 'test', 0),
                    array('code', '</p>', 7),
                )
            )
        );
    }
}
