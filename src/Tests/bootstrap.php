<?php

require_once __DIR__ . '/../../vendor/autoload.php';

if (!class_exists('PHPUnit_Framework_TestCase')
    && version_compare(phpversion(), '7.1') >= 0
) {
    define('HTML2PDF_PHPUNIT_VERSION', 9);
    require_once 'CrossVersionCompatibility/PhpUnit9/TestCase.php';
} else {
    define('HTML2PDF_PHPUNIT_VERSION', 5);
}
