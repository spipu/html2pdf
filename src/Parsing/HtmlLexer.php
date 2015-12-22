<?php
/**
 * Html2Pdf Library - parsing Html class
 *
 * HTML => PDF convertor
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
     * @return Token[]
     */
    public function tokenize($html)
    {
        // initialise the array
        $tokens = array();

        // regexp to separate the tags from the texts
        $reg = '/(<\/?\w[^<>]*>)|([^<]+|<)/is';

        // last match found
        $str = '';
        $offset = 0;

        // As it finds a match
        while (preg_match($reg, $html, $parse, PREG_OFFSET_CAPTURE, $offset)) {
            // if it is a tag
            if ($parse[1][0]) {
                // save the previous text if it exists
                if ($str !== '') {
                    $tokens[] = new Token('txt', $str);
                }

                // save the tag, with the offset
                $tokens[] = new Token('code', trim($parse[1][0]), $offset);

                // init the current text
                $str = '';
            } else { // else (if it is a text)
                // add the new text to the current text
                $str .= $parse[2][0];
            }

            // Update offset to the end of the match
            $offset = $parse[0][1] + strlen($parse[0][0]);
            unset($parse);
        }
        // if a text is present in the end, we save it
        if ($str != '') {
            $tokens[] = new Token('txt', $str);
        }

        return $tokens;
    }
}
