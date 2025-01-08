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
 * Table Exception
 */
class LocaleException extends Html2PdfException
{
    /**
     * ERROR CODE 5
     * @var int
     */
    const ERROR_CODE = 5;

    /**
     * @var string
     */
    protected $localCode;

    /**
     * set the code
     *
     * @param string $localCode
     *
     * @return $this
     */
    public function setLocaleCode($localCode)
    {
        $this->localCode = $localCode;

        return $this;
    }

    /**
     * get the local code
     *
     * @return string
     */
    public function getLocalCode()
    {
        return $this->localCode;
    }
}
