<?php

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Parsing\HtmlLexer;

/**
 * Class HtmlLexerTest
 */
class HtmlLexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $html
     * @param array  $expectedTokens
     *
     * @dataProvider tokenizeProvider
     */
    public function testTokenize($html, $expectedTokens)
    {
        $lexer = new HtmlLexer();
        $this->assertEquals($expectedTokens, $lexer->tokenize($html));
    }

    /**
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
