<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class TextParserTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\TextParserTestCase
    {
    }
} else {
    abstract class TextParserTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\TextParserTestCase
    {
    }
}
