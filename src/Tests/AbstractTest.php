<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class Html2PdfTest
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Html2Pdf
     */
    private $html2pdf;

    /**
     * Executed before each test
     */
    protected function setUp()
    {
        $this->html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', [0, 0, 0, 0]);
        $this->html2pdf->pdf->SetTitle('PhpUnit Test');
    }

    /**
     * Executed after each test
     */
    protected function tearDown()
    {
        $this->html2pdf->clean();
        $this->html2pdf = null;
    }

    /**
     * Get the object to test
     *
     * @return Html2Pdf
     */
    protected function getObject()
    {
        return $this->html2pdf;
    }
}
