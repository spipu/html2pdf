<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class SvgDrawerTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\SvgDrawerTestCase
    {
    }
} else {
    abstract class SvgDrawerTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\SvgDrawerTestCase
    {
    }
}
