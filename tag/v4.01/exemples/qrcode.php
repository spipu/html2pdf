<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF 
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 * 
 * isset($_GET['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le résultat au format HTML
 * si le paramètre 'vuehtml' est passé en paramètre _GET
 */
 	// récupération du contenu HTML
 	ob_start();
 	$msg = "Le site de html2pdf\r\nhttp://html2pdf.fr/";
?>
<page backtop="10mm" >
	<page_header>
		<table style="width: 100%; border: solid 1px black;">
			<tr>
				<td style="text-align: left;	width: 50%">html2pdf</td>
				<td style="text-align: right;	width: 50%">Exemples de QRcode</td>
			</tr>
		</table>
	</page_header>
	<h1>Exemples de QRcode</h1>
	<h3>Message avec Correction d'erreur L, M, Q, H (valeur par défaut : H)</h3>
	<qrcode value="<?php echo $msg; ?>" ec="L" style="width: 30mm;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" ec="M" style="width: 30mm;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" ec="Q" style="width: 30mm;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" ec="H" style="width: 30mm;"></qrcode>
	<br>
	<h3>Message avec différentes largeurs</h3>
	<qrcode value="<?php echo $msg; ?>" style="width: 20mm;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" style="width: 30mm;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" style="width: 40mm;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" style="width: 50mm;"></qrcode>
	<br>
	<h3>Message de différentes couleurs</h3>
	<qrcode value="<?php echo $msg; ?>" style="width: 40mm; background-color: white; color: black;"></qrcode>
	<qrcode value="<?php echo $msg; ?>" style="width: 40mm; background-color: yellow; color: red"></qrcode>
	<qrcode value="<?php echo $msg; ?>" style="width: 40mm; background-color: #FFCCFF; color: #003300"></qrcode>
	<qrcode value="<?php echo $msg; ?>" style="width: 40mm; background-color: #CCFFFF; color: #003333"></qrcode>
	<br>
	<h3>Message sans border</h3>
	<qrcode value="<?php echo $msg; ?>" style="border: none; width: 40mm;"></qrcode>
	<br>
</page>
<?php
 	$content = ob_get_clean();
	
	// conversion HTML => PDF
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	try
	{
		$html2pdf = new HTML2PDF('P','A4','fr', false, 'ISO-8859-15');
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output('qrcode.pdf');
	}
	catch(HTML2PDF_exception $e) { echo $e; }
	