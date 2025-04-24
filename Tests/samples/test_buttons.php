<?php
require_once '../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

try {
    $html2pdf = new Html2Pdf();
    
    // Load the test HTML
    $content = file_get_contents('button_test.html');
    
    // Convert to PDF
    $html2pdf->writeHTML($content);
    $html2pdf->output('button_test.pdf', 'D');
} catch (Exception $e) {
    echo $e->getMessage();
}
