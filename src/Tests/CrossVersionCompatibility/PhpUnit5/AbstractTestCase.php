<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

use Spipu\Html2Pdf\Html2Pdf;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Html2Pdf
     */
    protected $html2pdf;

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

    public function expectException($exception)
    {
        if (method_exists(\PHPUnit_Framework_TestCase::class, 'setExpectedException')) {
            $this->setExpectedException($exception);
        }
    }

    public function expectExceptionMessage($message, $exception = null)
    {
        if (method_exists(\PHPUnit_Framework_TestCase::class, 'expectExceptionMessage')) {
            parent::expectExceptionMessage($message);
        } elseif (method_exists(\PHPUnit_Framework_TestCase::class, 'setExpectedException')) {
            $this->setExpectedException($exception, $message);
        }
    }
}
