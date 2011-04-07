<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 */
	require_once(dirname(__FILE__).'/../html2pdf.class.php');

	// récupération de l'html
 	ob_start();
 	include(dirname('__FILE__').'/res/about.php');
	$content = ob_get_clean();

	// initialisation de HTML2PDF
	$html2pdf = new HTML2PDF('P','A4','fr', false, 'ISO-8859-15', array(0, 0, 0, 0));
	
	// affichage de la page en entier
	$html2pdf->pdf->SetDisplayMode('fullpage');
	
	// conversion
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	
	// ajout de l'index (obligatoirement en fin de génération)
	$html2pdf->createIndex('Sommaire', 30, 12, false, true, 2);
	
	// envoie du PDF
	$html2pdf->Output('about.pdf');
