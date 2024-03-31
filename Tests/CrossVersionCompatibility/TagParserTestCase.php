<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class TagParserTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9\TagParserTestCase
    {
    }
} else {
    abstract class TagParserTestCase extends \Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5\TagParserTestCase
    {
    }
}
