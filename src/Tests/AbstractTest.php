<?php

namespace Spipu\Html2Pdf\Tests;

use Spipu\Html2Pdf\Html2Pdf;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    require_once 'CrossVersionCompatibility/PhpUnit9/AbstractTestCase.php';
} else {
    require_once 'CrossVersionCompatibility/PhpUnit5/AbstractTestCase.php';
}

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\AbstractTestCase
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
