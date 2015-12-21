<?php 

namespace Spipu\Html2Pdf\Exception;

/**
 * Class InvalidHtmlTagException
 */
class InvalidHtmlTagException extends HtmlParsingException
{
    /**
     * @param int  $msg
     * @param null $other
     */
    public function __construct($msg, $other = null)
    {
        $this->message = $msg;
        parent::__construct(1, $other);
    }
}
