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
     * @param Html2PdfException $e
     */
    public function __construct(Html2PdfException $e)
    {
        $other = $e->getOther();
        // read the error
        switch ($e->getCode()) {
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

        $this->message = Locale::get('txt01', 'error: ').$e->getCode().' : '.strip_tags($msg);

        $this->buildHtmlMessage($msg, $e);

        $this->appendHtmlContent($e->getHTML());
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

    protected function buildHtmlMessage($msg, \Exception $e)
    {
        // create the HTML message
        $this->htmlMessage = '<span style="color: #AA0000; font-weight: bold;">'."\n";
        $this->htmlMessage.= Locale::get('txt01', 'error: ').$e->getCode().'</span><br>'."\n";
        $this->htmlMessage.= Locale::get('txt02', 'file:').' '.$e->getFile().'<br>'."\n";
        $this->htmlMessage.= Locale::get('txt03', 'line:').' '.$e->getLine().'<br>'."\n";
        $this->htmlMessage.= '<br>'."\n";
        $this->htmlMessage.= $msg . "\n";
    }

    protected function appendHtmlContent($html)
    {
        if ($html) {
            $this->htmlMessage .= "<br><br>HTML : ..." . trim(htmlentities($html)) . '...';
            $this->message .= ' HTML : ...'.trim($html).'...';
        }

    }
}
