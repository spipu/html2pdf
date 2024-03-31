<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class CssConverterTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\CssConverterTestCase
    {
    }
} else {
    abstract class CssConverterTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\CssConverterTestCase
    {
    }
}
