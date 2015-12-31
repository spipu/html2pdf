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
 * Class TokenStream
 */
class TokenStream
{
    private $index = 0;
    private $tokens;

    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @return Token
     */
    public function current()
    {
        if (count($this->tokens) <= $this->index) {
            return null;
        }
        return $this->tokens[$this->index];
    }

    /**
     * @return Token
     */
    public function next()
    {
        $this->index++;

        return $this->current();
    }

    public function count()
    {
        return count($this->tokens);
    }
}
