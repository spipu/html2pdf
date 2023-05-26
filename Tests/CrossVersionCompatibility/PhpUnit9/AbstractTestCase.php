<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9;

use PHPUnit\Framework\TestCase;
use Spipu\Html2Pdf\Html2Pdf;

abstract class AbstractTestCase extends TestCase
{
    use AssertContains;

    /**
     * @var Html2Pdf
     */
    protected $html2pdf;

    /**
     * Executed before each test
     */
    protected function setUp(): void
    {
        $this->html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', [0, 0, 0, 0]);
        $this->html2pdf->pdf->SetTitle('PhpUnit Test');
    }

    /**
     * Executed after each test
     */
    protected function tearDown(): void
    {
        $this->html2pdf->clean();
        $this->html2pdf = null;
    }

    public function expectExceptionMessage($message, $exception = null): void
    {
        // Yes, we ignore $exception
        parent::expectExceptionMessage($message);
    }
}
