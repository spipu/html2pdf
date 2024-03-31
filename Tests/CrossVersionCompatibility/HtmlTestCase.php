<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class HtmlTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\HtmlTestCase
    {
    }
} else {
    abstract class HtmlTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\HtmlTestCase
    {
    }
}
