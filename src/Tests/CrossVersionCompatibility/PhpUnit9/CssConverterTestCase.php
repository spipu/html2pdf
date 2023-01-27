<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility;

use Spipu\Html2Pdf\CssConverter;

abstract class CssConverterTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CssConverter
     */
    protected $cssConverter;

    public function setUp(): void
    {
        $this->cssConverter = new CssConverter();
    }
}
