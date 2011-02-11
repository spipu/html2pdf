<?php
/*
 * ATTENTION : 
 * Vous devez télécharger la librairie "QR-code generator >=0.98" (sous licence LGPL)
 * a cette adresse : http://prgm.spipu.net/php_qrcode
 * et mettre tout son contenu dans ce repertoire (qrcode)
 * en remplacant également ce fichier (qrcode.class.php)
 * 
 * WARNING: 
 * You have to download the librairy "QR-code generator >=0.98" (under LGPL licence)
 * at this url : http://prgm.spipu.net/php_qrcode
 * and to put all his content in this folder (qrcode)
 * and to replace also this file (qrcode.class.php)
 */

if (!defined('__CLASS_QRCODE__'))
{
	define('__CLASS_QRCODE__', true);
	
	class QRcode
	{
		public function __construct($value, $level='L')
		{
			echo '
<pre>
	<b>ATTENTION :</b> 
		Vous devez télécharger la librairie "QR-code generator >=0.98" (sous licence LGPL)
 		a cette adresse : <a href="http://prgm.spipu.net/php_qrcode" target="_blank">http://prgm.spipu.net/php_qrcode</a>
 		et mettre tout son contenu dans ce repertoire : '.dirname(__FILE__).'
 		en remplacant également ce fichier : '.basename(__FILE__).'
 		
 	<b>WARNING:</b> 
 		You have to download the librairy "QR-code generator >=0.98" (under LGPL licence)
 		at this url : <a href="http://prgm.spipu.net/php_qrcode" target="_blank">http://prgm.spipu.net/php_qrcode</a>
 		and to put all his contents in this folder '.dirname(__FILE__).'
 		and to replace also this file : '.basename(__FILE__).'
 </pre>';
			exit;
		}

		public function getQrSize() { return 0; }
		public function disableBorder() { }
		public function displayFPDF(&$fpdf, $x, $y, $s, $background=array(255,255,255), $color=array(0,0,0)) { return true; }
		public function displayTCPDF(&$tcpdf, $x, $y, $s, $background=array(255,255,255), $color=array(0,0,0)) { return true; }
		public function displayHTML() { return true; }
		public function displayPNG($s=4, $background=array(255,255,255), $color=array(0,0,0), $filename = null, $quality = 0) { return true; }
	}
}