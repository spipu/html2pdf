<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */

use Spipu\Html2Pdf\Html2Pdf;

    require_once(dirname(__FILE__).'/../vendor/autoload.php');

    $html2pdf = new Html2Pdf('P', 'A4', 'fr');

    // get the HTML
    $content = file_get_contents(K_PATH_MAIN.'examples/data/utf8test.txt');
    $content = '<page style="font-family: freeserif"><br />'.nl2br($content).'</page>';

    // convert to PDF
    try
    {
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('utf8.pdf');
    }
    catch(Html2Pdf_exception $e) {
        echo $e;
        exit;
    }
