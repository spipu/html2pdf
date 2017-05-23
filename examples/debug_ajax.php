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
require_once(dirname(__FILE__).'/../vendor/autoload.php');

try {
	ob_start();
	include dirname(__FILE__).'/res/debug.php';
	$content = ob_get_clean();

	$html2pdf = new HTML2PDF('P', 'A4', 'fr');
	$html2pdf->setModeDebug('ajax');
	$html2pdf->setDefaultFont('Arial');
	$html2pdf->writeHTML($content);
	$html2pdf->Output(__DIR__.'/debug.pdf','F');
	$debugContent = $html2pdf->getDebugContent();
	?>
	<h1>Debug.php</h1>
	<h2>Ceci est le r&eacute;sultat du debug</h2>
	<?php echo $debugContent;?>
	<h2>Et le fichier g&eacute;n&eacute;r&eacute;</h2>
	<iframe src="debug.pdf" style="width:100%;height:50%;border:1px solid #959595;"></iframe>
	<?php
} catch (Html2PdfException $e) {
	$formatter = new ExceptionFormatter($e);
	echo $formatter->getHtmlMessage();
}
