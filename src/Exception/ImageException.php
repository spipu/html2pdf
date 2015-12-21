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
 * Html2Pdf Library - ImageException
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
class ImageException extends Html2PdfException
{
    /**
     * ERROR CODE 2
     * @var int
     */
    const ERROR_CODE = 2;

    /**
     * asked unknown image
     * @var string
     */
    protected $_image;

    /**
     * set the image in error
     *
     * @param string $value the value
     *
     * @return ImageException
     */
    public function setImage($value)
    {
        $this->_image = $value;

        return $this;
    }

    /**
     * get the image in error
     *
     * @return string
     */
    public function getImage()
    {
        return $this->_image;
    }
}
