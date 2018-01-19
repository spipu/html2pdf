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

namespace Spipu\Html2Pdf\Tests\Debug;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Debug\Debug;

/**
 * Class DebugTest
 */
class DebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test Debug Mode, Automatic
     *
     * @return void
     */
    public function testAutomatic()
    {
        $html = '<p>First Tag</p>';
        $html.= '<div>Second Tag</div>';
        $html.= '<b>Third Tag</b>';

        // prepare the Html2Pdf object
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');

        // make the test
        ob_start();
        $object->setModeDebug();
        $object->writeHTML($html);
        $pdfResult = $object->output('test.pdf', 'S');
        $debugResult = ob_get_clean();

        $this->assertSame('', $pdfResult);
        $this->assertNotEmpty($debugResult);
    }

    /**
     * test Debug Mode, manual
     *
     * @return void
     */
    public function testManual()
    {
        $html = '<p>First Tag</p>';
        $html.= '<div>Second Tag</div>';
        $html.= '<b>Third Tag</b>';

        // Prepare debug object, without html output
        $debug = new Debug(false);

        // prepare the Html2Pdf object
        $object = new Html2Pdf();
        $object->pdf->SetTitle('PhpUnit Test');

        // make the test
        ob_start();
        $object->setModeDebug($debug);
        $object->writeHTML($html);
        $pdfResult = $object->output('test.pdf', 'S');
        $debugResult = ob_get_clean();

        $this->assertSame('', $pdfResult);
        $this->assertNotEmpty($debugResult);
    }

}
