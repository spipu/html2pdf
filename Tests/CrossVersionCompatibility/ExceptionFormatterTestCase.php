<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class ExceptionFormatterTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\ExceptionFormatterTestCase
    {
    }
} else {
    abstract class ExceptionFormatterTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\ExceptionFormatterTestCase
    {
    }
}
