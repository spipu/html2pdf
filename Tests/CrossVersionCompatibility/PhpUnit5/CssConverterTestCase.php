<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit5;

use PHPUnit_Framework_TestCase;
use Spipu\Html2Pdf\CssConverter;

abstract class CssConverterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var CssConverter
     */
    protected $cssConverter;

    protected function setUp()
    {
        $this->cssConverter = new CssConverter();
    }
}
