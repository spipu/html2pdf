<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF, utilise TCPDF 
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 * 
 * isset($_GET['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le résultat au format HTML
 * si le paramètre 'vuehtml' est passé en paramètre _GET
 */

 	// récupération du contenu HTML
 	$content = file_get_contents(dirname(__FILE__).'/../_tcpdf/cache/utf8test.txt');
 	$content = '<page style="font-family: freeserif"><br />'.nl2br($content).'</page>';
	
	// conversion HTML => PDF
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$html2pdf = new HTML2PDF('P','A4','fr', true, 'UTF-8');
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->Output('utf8.pdf');
