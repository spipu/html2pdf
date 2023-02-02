<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Tests\CrossVersionCompatibility\AbstractTestCase;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends AbstractTestCase
{
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
