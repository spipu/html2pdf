<?php

namespace Spipu\Html2Pdf\Exception;

use Spipu\Html2Pdf\Html2PdfException;
use Spipu\Html2Pdf\Locale;

/**
 * Class ExceptionFormatter
 */
class ExceptionFormatter
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $htmlMessage;

    /**
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        $this->message = Locale::get('txt01', 'error: ').$e->getCode().' : '.strip_tags($e->getMessage());

        $this->buildHtmlMessage($e);

        if ($e instanceof Html2PdfException) {
            $this->appendHtmlContent($e->getHTML());
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getHtmlMessage()
    {
        return $this->htmlMessage;
    }

    protected function buildHtmlMessage(\Exception $e)
    {
        // create the HTML message
        $this->htmlMessage = '<span style="color: #AA0000; font-weight: bold;">'."\n";
        $this->htmlMessage.= Locale::get('txt01', 'error: ').$e->getCode().'</span><br>'."\n";
        $this->htmlMessage.= Locale::get('txt02', 'file:').' '.$e->getFile().'<br>'."\n";
        $this->htmlMessage.= Locale::get('txt03', 'line:').' '.$e->getLine().'<br>'."\n";
        $this->htmlMessage.= '<br>'."\n";
        $this->htmlMessage.= $e->getMessage()."\n";
    }

    protected function appendHtmlContent($html)
    {
        if ($html) {
            $this->htmlMessage .= "<br><br>HTML : ..." . trim(htmlentities($html)) . '...';
            $this->message .= ' HTML : ...'.trim($html).'...';
        }

    }
}
