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

namespace Spipu\Html2Pdf;

class Html2PdfException extends \Exception
{
    protected $html = null;
    protected $other = null;

    /**
     * generate a Html2Pdf exception
     *
     * @param int    $code  error code
     * @param mixed  $other additional information
     * @param string $html  additional information
     *
     * @return Html2PdfException
     */
    public function __construct($code = 0, $other = null, $html = '')
    {
        $this->other = $other;
        // add the optional html content
        if ($html) {
            $this->html = $html;
        }

        // construct the exception
        $msg = $this->message ? $this->message : 'Html2Pdf exception';
        parent::__construct($msg, $code);
    }

    /**
     * get the message as string
     *
     * @access public
     * @return string $messageHtml
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * get the optional html code
     *
     * @access public
     * @return string $html
     */
    public function getHTML()
    {
        return $this->html;
    }

    public function getOther()
    {
        return $this->other;
    }
}
