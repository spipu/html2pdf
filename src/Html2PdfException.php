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

    /**
     * generate a Html2Pdf exception
     *
     * @param int    $err   error number
     * @param mixed  $other additionnal informations
     * @param string $html  additionnal informations
     *
     * @return Html2PdfException
     */
    final public function __construct($err = 0, $other = null, $html = '')
    {
        // read the error
        switch ($err) {
            case 1: // Unsupported tag
                $msg = (Locale::get('err01'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                break;

            case 2: // too long sentence
                $msg = (Locale::get('err02'));
                $msg = str_replace('[[OTHER_0]]', $other[0], $msg);
                $msg = str_replace('[[OTHER_1]]', $other[1], $msg);
                $msg = str_replace('[[OTHER_2]]', $other[2], $msg);
                break;

            case 3: // closing tag in excess
                $msg = (Locale::get('err03'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                break;

            case 4: // tags closed in the wrong order
                $msg = (Locale::get('err04'));
                $msg = str_replace('[[OTHER]]', print_r($other, true), $msg);
                break;

            case 5: // unclosed tag
                $msg = (Locale::get('err05'));
                $msg = str_replace('[[OTHER]]', print_r($other, true), $msg);
                break;

            case 6: // image can not be loaded
                $msg = (Locale::get('err06'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                break;

            case 7: // too big TD content
                $msg = (Locale::get('err07'));
                break;

            case 8: // SVG tag not in DRAW tag
                $msg = (Locale::get('err08'));
                $msg = str_replace('[[OTHER]]', $other, $msg);
                break;

            case 9: // deprecated
                $msg = (Locale::get('err09'));
                $msg = str_replace('[[OTHER_0]]', $other[0], $msg);
                $msg = str_replace('[[OTHER_1]]', $other[1], $msg);
                break;

            case 0: // specific error
            default:
                $msg = $other;
                break;
        }

        // add the optionnal html content
        if ($html) {
            $this->html = $html;
        }

        // construct the exception
        parent::__construct($msg, $err);
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
}
