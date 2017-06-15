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
    $content = "
<page>
    <h1>Test de JavaScript 2</h1><br>
    <br>
    Normalement une alerte devrait apparaitre, indiquant \"coucou\"
</page>";

    $html2pdf = new Html2Pdf('P', 'A4', 'fr');
    $html2pdf->pdf->IncludeJS("app.alert('coucou');");
    $html2pdf->writeHTML($content);
    $html2pdf->output('js2.pdf');
} catch (Html2PdfException $e) {
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
