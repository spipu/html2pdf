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

use PHPUnit\Framework\TestCase;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Exception\ImageException;
use Spipu\Html2Pdf\Exception\LongSentenceException;


/**
 * Class ExceptionFormaterTest
 */
class ExceptionFormatterTest extends TestCase
{
    /**
     * Test the formatter / generic exception
     */
    public function testGeneric()
    {
        $exception = new Html2PdfException('My Message');
        $formatter = new ExceptionFormatter($exception);

        $messages = [
            $formatter->getMessage(),
            $formatter->getHtmlMessage()
        ];

        foreach ($messages as $message) {
            $this->assertStringContainsString('Html2Pdf Error ['.Html2PdfException::ERROR_CODE.']', $message);
            $this->assertStringContainsString('My Message', $message);
        }
    }

    /**
     * Test the formatter / parsing exception
     */
    public function testParsing()
    {
        $exception = new HtmlParsingException('My Message');
        $exception->setInvalidTag('my_tag');
        $exception->setHtmlLine(42);

        $formatter = new ExceptionFormatter($exception);

        $messages = [
            $formatter->getMessage(),
            $formatter->getHtmlMessage()
        ];

        foreach ($messages as $message) {
            $this->assertStringContainsString('Html2Pdf Error ['.HtmlParsingException::ERROR_CODE.']', $message);
            $this->assertStringContainsString('My Message', $message);
            $this->assertStringContainsString('my_tag', $message);
            $this->assertStringContainsString('42', $message);
        }
    }

    /**
     * Test the formatter / image exception
     */
    public function testImage()
    {
        $exception = new ImageException('My Message');
        $exception->setImage('my_image.png');

        $formatter = new ExceptionFormatter($exception);

        $messages = [
            $formatter->getMessage(),
            $formatter->getHtmlMessage()
        ];

        foreach ($messages as $message) {
            $this->assertStringContainsString('Html2Pdf Error ['.ImageException::ERROR_CODE.']', $message);
            $this->assertStringContainsString('My Message', $message);
            $this->assertStringContainsString('my_image.png', $message);
        }
    }

    /**
     * Test the formatter / long sentence exception
     */
    public function testLongSentence()
    {
        $exception = new LongSentenceException('My Message');
        $exception->setSentence('my sentence');
        $exception->setLength(142);
        $exception->setWidthBox(242);

        $formatter = new ExceptionFormatter($exception);

        $messages = [
            $formatter->getMessage(),
            $formatter->getHtmlMessage()
        ];

        foreach ($messages as $message) {
            $this->assertStringContainsString('Html2Pdf Error ['.LongSentenceException::ERROR_CODE.']', $message);
            $this->assertStringContainsString('My Message', $message);
            $this->assertStringContainsString('my sentence', $message);
            $this->assertStringContainsString('142', $message);
            $this->assertStringContainsString('242', $message);
        }
    }
}
