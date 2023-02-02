<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class AbstractTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\AbstractTestCase
    {
    }
} else {
    abstract class AbstractTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\AbstractTestCase
    {
    }
}
