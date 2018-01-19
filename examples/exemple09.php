<?php
/**
 * Html2Pdf Library - example
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
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

if (isset($_SERVER['REQUEST_URI'])) {
    $generate = isset($_GET['make_pdf']);
    $nom = isset($_GET['nom']) ? $_GET['nom'] : 'inconnu';
    $url = dirname($_SERVER['REQUEST_URI']);
    if (substr($url, 0, 7)!=='http://') {
        $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
    }
} else {
    $generate = true;
    $nom = 'spipu';
    $url = 'http://localhost/html2pdf/examples/';
}

$nom = substr(preg_replace('/[^a-zA-Z0-9]/isU', '', $nom), 0, 26);
$url.= '/res/exemple09.png.php?px=5&amp;py=20';


if ($generate) {
    ob_start();
} else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
        <title>Exemple d'auto génération de PDF</title>
    </head>
    <body>
<?php
}
?>
<br>
Ceci est un exemple de génération de PDF via un bouton :)<br>
<br>
<img src="<?php echo $url; ?>" alt="image_php" ><br>
<br>
<?php
if ($generate) {
?>
Bonjour <b><?php echo $nom; ?></b>, ton nom peut s'écrire : <br>
<barcode type="C39" value="<?php echo strtoupper($nom); ?>" style="color: #770000" ></barcode><hr>
<br>
<?php
}
?>
<br>
<?php
if ($generate) {
    $content = ob_get_clean();

    try {
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($content);
        $html2pdf->output('exemple09.pdf');
        exit;
    } catch (Html2PdfException $e) {
        $html2pdf->clean();

        $formatter = new ExceptionFormatter($e);
        echo $formatter->getHtmlMessage();
        exit;
    }
}
?>
        <form method="get" action="">
            <input type="hidden" name="make_pdf" value="">
            Ton nom : <input type="text" name="nom" value=""> -
            <input type="submit" value="Generer le PDF" >
        </form>
    </body>
</html>
