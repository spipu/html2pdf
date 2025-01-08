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
 * Html2Pdf Exception
 */
class Html2PdfException extends \Exception
{
    /**
     * ERROR CODE 0
     * @var int
     */
    const ERROR_CODE = 0;

    /**
     * Construct the exception.
     *
     * @param string $message The Exception message to throw.
     *
     * @return Html2PdfException
     */
    public function __construct($message)
    {
        parent::__construct($message, static::ERROR_CODE);
    }
}
