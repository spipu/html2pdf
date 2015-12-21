<?php 
/**
 * Html2Pdf Library - Exception
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
 * Html2Pdf Library - Html2PdfException
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
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
    protected $_invalidTag;

    /**
     * the html part that has the pb
     * @var string
     */
    protected $_htmlPart;

    /**
     * set the invalid Tag
     *
     * @param string $value the value
     *
     * @return HtmlParsingException
     */
    public function setInvalidTag($value)
    {
        $this->_invalidTag = $value;

        return $this;
    }

    /**
     * get the invalid Tag
     *
     * @return string
     */
    public function getInvalidTag()
    {
        return $this->_invalidTag;
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
        $this->_htmlPart = $value;

        return $this;
    }

    /**
     * get the html part
     *
     * @return string
     */
    public function getHtmlPart()
    {
        return $this->_htmlPart;
    }
}
