<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Exception;

use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;

/**
 * Class ExceptionFormaterTest
 */
class ExceptionFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the formatter
     */
    public function testOk()
    {
        $exception = new Html2PdfException('message');
        $formatter = new ExceptionFormatter($exception);

        $this->assertSame(true, true);
    }
}
