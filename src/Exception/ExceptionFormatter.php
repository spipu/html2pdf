<?php
/**
 * Html2Pdf Library - Exception class
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Exception;

/**
 * Exception Formatter
 */
class ExceptionFormatter
{
    /**
     * the text message
     * @var string
     */
    protected $message;

    /**
     * the html message
     * @var string
     */
    protected $htmlMessage;

    /**
     * PHP Constructor
     *
     * @param Html2PdfException $e the exception to format
     *
     * @return ExceptionFormatter
     */
    public function __construct(Html2PdfException $e)
    {
        $data = $this->getAdditionalData($e);

        $this->buildTextMessage($e, $data);
        $this->buildHtmlMessage($e, $data);
    }

    /**
     * get the txt message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * get tht HTML message
     *
     * @return string
     */
    public function getHtmlMessage()
    {
        return $this->htmlMessage;
    }

    /**
     * get the additional data from the exception
     *
     * @param Html2PdfException $e the exception to display
     *
     * @return array
     */
    protected function getAdditionalData(Html2PdfException $e)
    {
        $data = array();

        // read the error
        switch ($e->getCode()) {
            case HtmlParsingException::ERROR_CODE:
                /** @var HtmlParsingException $e */
                $data['invalid tag'] = $e->getInvalidTag();
                $data['html line'] = $e->getHtmlLine();
                break;

            case ImageException::ERROR_CODE:
                /** @var ImageException $e */
                $data['image src'] = $e->getImage();
                break;

            case LongSentenceException::ERROR_CODE:
                /** @var LongSentenceException $e */
                $data['sentence']  = $e->getSentence();
                $data['box width'] = $e->getWidthBox();
                $data['length']    = $e->getLength();
                break;

            case TableException::ERROR_CODE:
            case Html2PdfException::ERROR_CODE:
            default:
                break;
        }

        return $data;
    }

    /**
     * Build the text message
     *
     * @param Html2PdfException $e    the exception of the error
     * @param array             $data additionnal data
     *
     * @return void
     */
    protected function buildTextMessage(Html2PdfException $e, $data)
    {
        $this->message = 'Html2Pdf Error ['.$e->getCode().']'."\n";
        $this->message.= $e->getMessage()."\n";
        $this->message.= ' File: '.$e->getFile()."\n";
        $this->message.= ' Line: '.$e->getLine()."\n";

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->message .= ' '.ucwords($key).': '.trim($value)."\n";
            }
        }
    }

    /**
     * build the html message
     *
     * @param Html2PdfException $e    the exception of the error
     * @param array             $data additional data
     *
     * @return void
     */
    protected function buildHtmlMessage(Html2PdfException $e, $data)
    {
        $this->htmlMessage = '<span style="color: #A00; font-weight: bold;">';
        $this->htmlMessage.= 'Html2Pdf Error ['.$e->getCode().']';
        $this->htmlMessage.= '</span><br />'."\n";
        $this->htmlMessage.= htmlentities($e->getMessage())."<br />\n";
        $this->htmlMessage.= ' File: '.$e->getFile()."<br />\n";
        $this->htmlMessage.= ' Line: '.$e->getLine()."<br />\n";

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->htmlMessage .= ' '.ucwords($key).': '.trim(htmlentities($value))."<br />\n";
            }
        }
    }
}
