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
use Spipu\Html2Pdf\Html2PdfException;

    // get the HTML
     ob_start();
?>
<style type="text/css">
<!--
    table
    {
        padding: 0;
        margin: 0;
        border: none;
        border-right: solid 0.2mm black;
    }
    td
    {
        padding: 0;
        margin: 0;
        border: none;
    }

    img
    {
        width: 10mm;
    }
-->
</style>
<page>
<table cellpadding="0" cellspacing="0"><tr>
<?php for($k=0; $k<28; $k++) echo '<td><img src="./res/regle.png" alt="" ><br>'.$k.'</td>'; ?>
</tr></table>
</page>
<?php
     $content = ob_get_clean();

    // convert to PDF
    require_once(dirname(__FILE__).'/../vendor/autoload.php');
    try
    {
        $html2pdf = new Html2Pdf('L', 'A4', 'fr', true, 'UTF-8', 10);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('regle.pdf');
    }
    catch(Html2PdfException $e) {
        echo $e;
        exit;
    }
