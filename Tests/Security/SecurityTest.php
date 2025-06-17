<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Security;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Security\Security;
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Div Tag test
 */
class SecurityTest extends AbstractTest
{
    public function testOk()
    {
        $security = new Security();
        $security->addAllowedHost('www.html2pdf.fr');
        $security->checkValidPath('https://www.html2pdf.fr/img/_langue/en/logo.gif');
        $this->assertTrue(true);
    }

    public function testBadScheme()
    {
        $this->expectException(HtmlParsingException::class);
        $this->expectExceptionMessage('Unauthorized path scheme', HtmlParsingException::class);

        $security = new Security();
        $security->checkValidPath('phar://test.com/php.phar');
    }

    public function testBadHost()
    {
        $this->expectException(HtmlParsingException::class);
        $this->expectExceptionMessage('Unauthorized path host', HtmlParsingException::class);

        $security = new Security();
        $security->checkValidPath('https://www.html2pdf.fr/img/_langue/en/logo.gif');
    }
}
