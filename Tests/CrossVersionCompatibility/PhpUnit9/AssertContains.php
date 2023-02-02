<?php

namespace Spipu\Html2Pdf\Tests\CrossVersionCompatibility\PhpUnit9;

trait AssertContains
{
    public static function assertContains($needle, $haystack, string $message = ''): void
    {
        if (is_string($haystack)) {
            parent::assertStringContainsString($needle, $haystack, $message);
        } else {
            parent::assertContains($needle, $haystack, $message);
        }
    }
}
