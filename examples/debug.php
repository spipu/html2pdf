<?php
/**
 * Html2Pdf Library - example
*
* HTML => PDF converter
* distributed under the LGPL License
*
* @package   Html2pdf
* @author    Olivier VARROT <contact@evoweb.fr>
* @copyright 2016 Olivier VARROT
*/
require_once dirname(__FILE__).'/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
	ob_start();
	include dirname(__FILE__).'/res/debug.php';
	$content = ob_get_clean();

	$html2pdf = new Html2Pdf('P', 'A4', 'fr');
	$html2pdf->setModeDebug();
	$html2pdf->setDefaultFont('Arial');
	$html2pdf->writeHTML($content);
	$html2pdf->Output(__DIR__.'/debug.pdf','F');
} catch (Html2PdfException $e) {
	$formatter = new ExceptionFormatter($e);
	echo $formatter->getHtmlMessage();
}
