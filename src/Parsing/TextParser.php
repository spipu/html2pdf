<?php
/**
 * Html2Pdf Library - parsing Html class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Parsing;

/**
 * Class TextParser
 */
class TextParser
{
    /**
     * @var string
     */
    private $encoding;

    /**
     * @param string $encoding
     */
    public function __construct($encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
    }

    /**
     * prepare the text
     *
     * @param   string $txt
     * @param   boolean $spaces true => replace multiple space+\t+\r+\n by a single space
     * @return  string txt
     * @access  protected
     */
    public function prepareTxt($txt, $spaces = true)
    {
        if ($spaces) {
            $txt = preg_replace('/\s+/isu', ' ', $txt);
        }
        $txt = str_replace('&euro;', '€', $txt);
        $txt = html_entity_decode($txt, ENT_QUOTES, $this->encoding);
        return $txt;
    }
}
