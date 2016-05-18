<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Parsing;

/**
 * Class HtmlLexer
 */
class HtmlLexer
{
    /**
     * Tokenize the HTML code
     *
     * @param string $html HTML code to tokenize
     *
     * @return TokenStream
     */
    public function tokenize($html)
    {
        // initialise the array
        $tokens = array();

        // regexp to separate the tags from the texts
        $reg = '/(<\/?\w[^<>]*>)|([^<]+|<)/is';
        $commentRegex = '/(<!--.*-->)/isU';

        // last match found
        $str = '';
        $offset = 0;
        $line = 1;
        $length = strlen($html);

        // As it finds a match
        while ($offset < $length) {
            if (strpos($html, '<!--', $offset) === $offset
                && preg_match($commentRegex, $html, $match, PREG_OFFSET_CAPTURE, $offset)
            ) {
                $line += substr_count($match[1][0], "\n");
                $offset = $match[0][1] + strlen($match[0][0]);
                continue;
            }
            preg_match($reg, $html, $parse, PREG_OFFSET_CAPTURE, $offset);
            // if it is a tag
            if ($parse[1][0]) {
                // save the previous text if it exists
                if ($str !== '') {
                    $tokens[] = new Token(Token::TEXT_TYPE, $str);
                }
                $tag = trim($parse[1][0]);
                if (substr($tag, 1, 1) === '/') {
                    $type = Token::TAG_CLOSE_TYPE;
                } elseif (substr($tag, -2, 1) === '/') {
                    $type = Token::TAG_AUTOCLOSE_TYPE;
                } else {
                    $type = Token::TAG_OPEN_TYPE;
                }

                // save the tag, with the offset
                $tokens[] = new Token($type, $tag, $line);
                $line += substr_count($parse[1][0], "\n");

                // init the current text
                $str = '';
            } else { // else (if it is a text)
                // add the new text to the current text
                $str .= $parse[2][0];
                $line += substr_count($parse[2][0], "\n");
            }

            // Update offset to the end of the match
            $offset = $parse[0][1] + strlen($parse[0][0]);
            unset($parse);
        }
        // if a text is present in the end, we save it
        if ($str != '') {
            $tokens[] = new Token(Token::TEXT_TYPE, $str);
        }

        return new TokenStream($tokens);
    }
}
