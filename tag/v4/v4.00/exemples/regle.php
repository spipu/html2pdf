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
	
	// conversion HTML => PDF
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$html2pdf = new HTML2PDF('L','A4','fr', false, 'ISO-8859-15', 10);
	$html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->Output('regle.pdf');
