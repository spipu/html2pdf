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
 * Html2Pdf Library - LongSentenceException
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
class LongSentenceException extends Html2PdfException
{
    /**
     * ERROR CODE 3
     * @var int
     */
    const ERROR_CODE = 3;

    /**
     * the sentence
     * @var string
     */
    protected $_sentence;

    /**
     * the width of the box
     * @var string
     */
    protected $_widthBox;

    /**
     * the length of the sentence
     * @var string
     */
    protected $_length;

    /**
     * set the sentence
     *
     * @param string $value the value
     *
     * @return LongSentenceException
     */
    public function setSentence($value)
    {
        $this->_sentence = $value;

        return $this;
    }

    /**
     * get the image in error
     *
     * @return string
     */
    public function getSentence()
    {
        return $this->_sentence;
    }

    /**
     * set the width of the box that contain the sentence
     *
     * @param string $value the value
     *
     * @return LongSentenceException
     */
    public function setWidthBox($value)
    {
        $this->_widthBox = $value;

        return $this;
    }

    /**
     * get the image in error
     *
     * @return string
     */
    public function getWidthBox()
    {
        return $this->_widthBox;
    }

    /**
     * set the length
     *
     * @param string $value the value
     *
     * @return LongSentenceException
     */
    public function setLength($value)
    {
        $this->_length = $value;

        return $this;
    }

    /**
     * get the image in error
     *
     * @return string
     */
    public function getLength()
    {
        return $this->_length;
    }
}
