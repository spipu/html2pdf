<?php
/**
 * Html2Pdf Library - Exception class
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
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
     * the html part that has the pb
     * @var string
     */
    protected $htmlPart;

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
     * set the html part
     *
     * @param string $value the value
     *
     * @return HtmlParsingException
     */
    public function setHtmlPart($value)
    {
        $this->htmlPart = $value;

        return $this;
    }

    /**
     * get the html part
     *
     * @return string
     */
    public function getHtmlPart()
    {
        return $this->htmlPart;
    }
}
