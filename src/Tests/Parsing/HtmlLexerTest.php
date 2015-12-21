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
        $this->assertEquals($expectedTokens, $lexer->tokenize($html));
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
                    array('txt', 'test'),
                    array('code', '</p>', 7),
                )
            )
        );
    }
}
