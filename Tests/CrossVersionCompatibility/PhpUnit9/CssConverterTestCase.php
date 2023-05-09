<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9;

use PHPUnit\Framework\TestCase;
use Spipu\Html2Pdf\CssConverter;

abstract class CssConverterTestCase extends TestCase
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
