<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
require_once dirname(__FILE__).'/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    ob_start();
    include dirname(__FILE__).'/res/exemple00.php';
    $content = ob_get_clean();

    $html2pdf = new Html2Pdf('P', 'A4', 'fr');  //create a new PDF document in portrait orientation, in A4 format, and set French as default language
    $html2pdf->setDefaultFont('Arial');         //set default Font family of created PDF document as 'Arial'
    $html2pdf->writeHTML($content);             //write the HTML contents into created PDF document
    $html2pdf->output('example00.pdf');         //name created PDF document as "example00.pdf" and send it to browser
} catch (Html2PdfException $e) {                //catch any error occurs from codes at "try" part
    $html2pdf->clean();                         

    $formatter = new ExceptionFormatter($e);    
    echo $formatter->getHtmlMessage();          //display exception
}
