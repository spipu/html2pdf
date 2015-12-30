<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
require_once dirname(__FILE__).'/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    $content = "
<page>
    <h1>Test de JavaScript 3</h1><br>
    <br>
    Normalement une valeur devrait vous être demandée, puis affichée
</page>";

    $script = "var rep = app.response('Donnez votre nom'); app.alert('Vous vous appelez '+rep);";

    $html2pdf = new Html2Pdf('P', 'A4', 'fr');
    $html2pdf->pdf->IncludeJS($script);
    $html2pdf->writeHTML($content);
    $html2pdf->Output('js3.pdf');
} catch (Html2PdfException $e) {
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
