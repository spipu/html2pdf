<?php

require_once __DIR__ . '/../../vendor/autoload.php';

if (!class_exists('PHPUnit_Framework_TestCase') && version_compare(phpversion(), '7.1') >= 0) {
    class PHPUnit_Framework_TestCase extends PHPUnit\Framework\TestCase
    {
    }

    define('HTML2PDF_PHPUNIT_VERSION', 9);
} else {
    define('HTML2PDF_PHPUNIT_VERSION', 5);
}
