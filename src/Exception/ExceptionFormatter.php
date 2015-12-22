<?php
/**
 * Html2Pdf Library - Formatter Exception
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
 * Class ExceptionFormatter
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
class ExceptionFormatter
{
    /**
     * the text message
     * @var string
     */
    protected $_message;

    /**
     * the html message
     * @var string
     */
    protected $_htmlMessage;

    /**
     * PHP Constructor
     *
     * @param Html2PdfException $e the exception to format
     *
     * @return ExceptionFormatter
     */
    public function __construct(Html2PdfException $e)
    {
        $data = $this->_getAdditionnalData($e);

        $this->_buildTextMessage($e, $data);
        $this->_buildHtmlMessage($e, $data);
    }

    /**
     * get the txt message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * get tht HTML message
     *
     * @return string
     */
    public function getHtmlMessage()
    {
        return $this->_htmlMessage;
    }

    /**
     * get the additionnal data from the exception
     *
     * @param Html2PdfException $e the exception to display
     *
     * @return array
     */
    protected function _getAdditionnalData(Html2PdfException $e)
    {
        $data = array();

        // read the error
        switch ($e->getCode()) {
            case HtmlParsingException::ERROR_CODE:
                $data['invalid tag'] = $e->getInvalidTag();
                if ($e->getHtmlPart()) {
                    $data['html part'] = '... '.$e->getHtmlPart().' ...';
                }
                break;

            case ImageException::ERROR_CODE:
                $data['image src'] = $e->getImage();
                break;

            case LongSentenceException::ERROR_CODE:
                $msg = $e->getMessage();
                $data['sentence']  = $e->getSentence();
                $data['bow width'] = $e->getWidthBox();
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
    protected function _buildTextMessage(Html2PdfException $e, $data)
    {
        $this->_message = 'Html2Pdf Error ['.$e->getCode().']'."\n";
        $this->_message.= $e->getMessage()."\n";
        $this->_message.= ' File: '.$e->getFile()."\n";
        $this->_message.= ' Line: '.$e->getLine()."\n";

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->_message .= ' '.ucwords($key).': '.trim($value)."\n";
            }
        }
    }

    /**
     * build the html message
     *
     * @param Html2PdfException $e    the exception of the error
     * @param array             $data additionnal data
     *
     * @return void
     */
    protected function _buildHtmlMessage(Html2PdfException $e, $data)
    {
        $this->_htmlMessage = '<span style="color: #A00; font-weight: bold;">';
        $this->_htmlMessage.= 'Html2Pdf Error ['.$e->getCode().']';
        $this->_htmlMessage.= '</span><br />'."\n";
        $this->_htmlMessage.= htmlentities($e->getMessage())."<br />\n";
        $this->_htmlMessage.= ' File: '.$e->getFile()."<br />\n";
        $this->_htmlMessage.= ' Line: '.$e->getLine()."<br />\n";

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->_htmlMessage .= ' '.ucwords($key).': '.trim(htmlentities($value))."<br />\n";
            }
        }
    }
}
