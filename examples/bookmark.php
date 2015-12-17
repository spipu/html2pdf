<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
require_once dirname(__FILE__).'/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Html2PdfException;

// get the HTML
ob_start();
require dirname(__FILE__).'/res/bookmark.php';
$content = ob_get_clean();

// convert to PDF
try {
    $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', 0);
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->createIndex('Sommaire', 25, 12, false, true, 1);
    $html2pdf->Output('bookmark.pdf');
} catch (Html2PdfException $e) {
    echo $e;
    exit;
}
