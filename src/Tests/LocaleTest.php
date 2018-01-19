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

namespace Spipu\Html2Pdf\Tests\Tag;

use Spipu\Html2Pdf\Locale;
use Spipu\Html2Pdf\Exception\LocaleException;

/**
 * Class LocaleTest
 */
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test bad code
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\LocaleException
     */
    public function testBadCode()
    {
        Locale::clean();

        try {
            Locale::load('$aa');
        } catch (LocaleException $e) {
            $this->assertSame('$aa', $e->getLocalCode());
            throw $e;
        }
    }

    /**
     * test unknown code
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\LocaleException
     */
    public function testUnknownCode()
    {
        Locale::clean();
        try {
            Locale::load('aa');
        } catch (LocaleException $e) {
            $this->assertSame('aa', $e->getLocalCode());
            throw $e;
        }
    }

    /**
     * test good code
     *
     * @return void
     */
    public function testGoodCode()
    {
        Locale::clean();
        Locale::load('en');

        $this->assertSame('Page [[page_cu]]/[[page_nb]]', Locale::get('pdf04'));
        $this->assertSame('bad_return', Locale::get('bad_code', 'bad_return'));
        Locale::clean();
        $this->assertSame('bad_return', Locale::get('pdf04', 'bad_return'));
    }
}
