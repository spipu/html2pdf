<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 *
 * setTestTdInOnePage : permet de desactiver le test sur la taille des TD afin qu'ils rentrent sur une seule page
 * 
 * isset($_GET['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le résultat au format HTML
 * si le paramètre 'vuehtml' est passé en paramètre _GET
 */
 	// récupération du contenu HTML
	ob_start();
 	include(dirname(__FILE__).'/res/exemple11.php');
	$content = ob_get_clean();

	// conversion HTML => PDF
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	try
	{
		$html2pdf = new HTML2PDF('P','A4','fr',  false, 'ISO-8859-15');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output('exemple11.pdf');
	}
	catch(HTML2PDF_exception $e) { echo $e; }