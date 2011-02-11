<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 * 
 * IncludeJS : permet d'inclure du Javascript au format PDF
 * 
 * isset($_GET['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le résultat au format HTML
 * si le paramètre 'vuehtml' est passé en paramètre _GET
 */
 	ob_start();
?>
<page>
	<h1>Test de JavaScript 2</h1><br>
	<br>
	Normalement une alerte devrait apparaitre, indiquant "coucou"
</page>
<?php
	$content = ob_get_clean();
	
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$html2pdf = new HTML2PDF('P','A4','fr', false, 'ISO-8859-15');
	$html2pdf->pdf->IncludeJS("app.alert('coucou');");
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->Output('js2.pdf');
