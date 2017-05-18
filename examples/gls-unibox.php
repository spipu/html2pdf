<?php

/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Pierre Tomasina <tomasina@plab.io>
 * @copyright 2016 Laurent Pierre Tomasina
 */
require_once dirname(__FILE__).'/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

/*** API GLS PARAMS */

$CustomerID = '2500000001';
$ContactID = '2500000002';

$T8915 = $CustomerID;
$T8914 = $ContactID;
$T8700 = 'FR0057';
$T862 = '';

/*** */

$rescuesParams = [
    1  => 'A',
    2  => '2500000001',
    3  => '2500000002',
    4  => 'AA',
    5  => '250',
    6  => '57185',
    7  => '001',
    8  => '001',
    9  => '45694',
    10 => 'John Doe',
    11 => 'BAT F',
    12 => 'hall 37',
    13 => 'RUE DE l\'EMPIRE',
    14 => '42',
    15 => "ETOILE NOIR",
    16 => '0601020304',
    17 => '001-20160415-K93W',
    18 => '0200000000010000FR',
    19 => '01.00',
    20 => '',
];

$rescueMatrix = implode('|', $rescuesParams);
$rescueMatrixLen = strlen($rescueMatrix);
$lenRequired = 304;

$rescueMatrix = str_pad($rescueMatrix, $lenRequired - 1, ' ', STR_PAD_RIGHT) . '|';

ob_start();
include __DIR__ . '/gls-unibox.phtml';
$content = ob_get_clean();

try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', [0, 0, 0, 0]);
    $html2pdf->pdf->SetDisplayMode('fullpage');

    $html2pdf->addFont('swiss721', '', __DIR__ . '/res/swiss721.php');
    $html2pdf->setDefaultFont('swiss721');

    $html2pdf->writeHTML($content);
    $html2pdf->Output(__DIR__ . DIRECTORY_SEPARATOR . 'gls-unibox.pdf', 'F');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}