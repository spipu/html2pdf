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
?>
<page>
    <h1>Test de JavaScript 2</h1><br>
    <br>
    Normalement une alerte devrait apparaitre, indiquant "coucou"
</page>
<?php
$content = ob_get_clean();

// convert to PDF
try {
    $html2pdf = new Html2Pdf('P', 'A4', 'fr');
    $html2pdf->pdf->IncludeJS("app.alert('coucou');");
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output('js2.pdf');
} catch (Html2PdfException $e) {
    echo $e;
    exit;
}
