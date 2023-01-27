<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

use Spipu\Html2Pdf\CssConverter;

abstract class CssConverterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CssConverter
     */
    protected $cssConverter;

    public function setUp()
    {
        $this->cssConverter = new CssConverter();
    }
}
