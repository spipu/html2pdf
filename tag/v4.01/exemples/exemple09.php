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
 
 $generate = isset($_GET['make_pdf']);
 $nom = isset($_GET['nom']) ? $_GET['nom'] : 'inconnu';
 
 $nom = substr(preg_replace('/[^a-zA-Z0-9]/isU', '', $nom), 0, 26);
 
 if ($generate)
 {
 	ob_start();
 }
 else
 {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >	
		<title>Exemple d'auto génération de PDF</title>
	</head>
	<body>
<?php	
 }
?>
<br>
Ceci est un exemple de génération de PDF via un bouton :)<br>
<br>
<img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']); ?>/res/exemple09.png.php?px=5&amp;py=20" alt="image_php" ><br>
<br>
<?php if ($generate) { ?>
Bonjour <b><?php echo $nom; ?></b>, ton nom peut s'écrire : <br>
<barcode type="C39" value="<?php echo strtoupper($nom); ?>" style="color: #770000" ></barcode><hr>
<br>
<?php } ?>
<br>
<?php
	if ($generate)
	{
		$content = ob_get_clean();
		require_once(dirname(__FILE__).'/../html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P','A4', 'fr', false, 'ISO-8859-15');
			$html2pdf->writeHTML($content);
			$html2pdf->Output('exemple09.pdf');
		}
		catch(HTML2PDF_exception $e) { echo $e; }
		exit;
	}
?>
		<form method="get" action="">
			<input type="hidden" name="make_pdf" value="">
			Ton nom : <input type="text" name="nom" value=""> - 
			<input type="submit" value="Generer le PDF" >
		</form>
	</body>
</html>