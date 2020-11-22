<?php
/**
 * Html2Pdf Library - test performance
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

$nb = (count($argv)>1 ? (int) $argv[1] : 1);
ob_start();
?>
<page>
    <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #F7F7F7; font-size: 10pt;">
        <colgroup>
            <col style="width: 12%; text-align: left">
            <col style="width: 52%; text-align: left">
            <col style="width: 13%; text-align: right">
            <col style="width: 10%; text-align: center">
            <col style="width: 13%; text-align: right">
        </colgroup>
        <tbody>
<?php
   for ($k = 0; $k < $nb; $k++):
        $qty   = rand(1, 20);
        $price = rand(100, 9999)/100.;
?>
            <tr>
                <td><?php echo rand(100000, 999999); ?></td>
                <td>My product N°<?php echo rand(1, 100); ?></td>
                <td><?php echo number_format($price, 2, ',', ' '); ?> &euro;</td>
                <td><?php echo $qty; ?></td>
                <td><?php echo number_format($price*$qty, 2, ',', ' '); ?> &euro;</td>
            </tr>
<?php endfor; ?>
        </tbody>
    </table>
</page>
<?php
$content = ob_get_clean();

$start = microtime(true);

$html2pdf = new Html2Pdf('P', 'A4', 'fr');
$html2pdf->writeHTML($content);
$html2pdf->output('performance.pdf', 'S');

$delta = floor((microtime(true) - $start)*1000);
$memory = floor(memory_get_peak_usage()/1024);
echo "$delta|$memory\n";
