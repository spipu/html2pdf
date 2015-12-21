<?php 

namespace Spipu\Html2Pdf\Exception;

class UnclosedHtmlTagException extends HtmlParsingException
{
    public function __construct($msg, $other = null)
    {
        $this->message = $msg;
        parent::__construct(5, $other);
    }
}
