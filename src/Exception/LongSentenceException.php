<?php
/**
 * Html2Pdf Library - Exception class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Exception;

/**
 * Long Sentence Exception
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
    protected $sentence;

    /**
     * the width of the box
     * @var string
     */
    protected $widthBox;

    /**
     * the length of the sentence
     * @var string
     */
    protected $length;

    /**
     * set the sentence
     *
     * @param string $value the value
     *
     * @return LongSentenceException
     */
    public function setSentence($value)
    {
        $this->sentence = $value;

        return $this;
    }

    /**
     * get the image in error
     *
     * @return string
     */
    public function getSentence()
    {
        return $this->sentence;
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
        $this->widthBox = $value;

        return $this;
    }

    /**
     * get the image in error
     *
     * @return string
     */
    public function getWidthBox()
    {
        return $this->widthBox;
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
        $this->length = $value;

        return $this;
    }

    /**
     * get the length
     *
     * @return string
     */
    public function getLength()
    {
        return $this->length;
    }
}
