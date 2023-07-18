<?php
/**
 * Html2Pdf Library - Exception class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Exception;

/**
 * Html Parsing Exception
 */
class HtmlParsingException extends Html2PdfException
{
    /**
     * ERROR CODE 1
     * @var int
     */
    const ERROR_CODE = 1;

    /**
     * invalid tag
     * @var string
     */
    protected $invalidTag;

    /**
     * the line in HTML data where the error occurred
     * @var int
     */
    protected $htmlLine;

    /**
     * set the invalid Tag
     *
     * @param string $value the value
     *
     * @return HtmlParsingException
     */
    public function setInvalidTag($value)
    {
        $this->invalidTag = $value;

        return $this;
    }

    /**
     * get the invalid Tag
     *
     * @return string
     */
    public function getInvalidTag()
    {
        return $this->invalidTag;
    }

    /**
     * @param int $lineNumber the value
     *
     * @return HtmlParsingException
     */
    public function setHtmlLine($lineNumber)
    {
        $this->htmlLine = $lineNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getHtmlLine()
    {
        return $this->htmlLine;
    }
}
