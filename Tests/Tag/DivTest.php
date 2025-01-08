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

namespace Spipu\Html2Pdf\Tests\Tag;

use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Div Tag test
 */
class DivTest extends AbstractTest
{
    /**
     * test No Break
     *
     * @return void
     */
    public function testNoBreak()
    {
        $html = '<p>First Tag</p>';
        $html.= '<div>Second Tag</div>';
        $html.= '<p>Third Tag</p>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $result = $object->output('test.pdf', 'S');

        $this->assertNotEmpty($result);
        $this->assertSame(1, $object->getNbPages());
    }

    /**
     * test Break Before
     *
     * @return void
     */
    public function testBreakBefore()
    {
        $html = '<p>First Tag</p>';
        $html.= '<div style="page-break-before:always">Second Tag</div>';
        $html.= '<p>Third Tag</p>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $result = $object->output('test.pdf', 'S');

        $this->assertNotEmpty($result);
        $this->assertSame(2, $object->getNbPages());
    }

    /**
     * test Break After
     *
     * @return void
     */
    public function testBreakAfter()
    {
        $html = '<p>First Tag</p>';
        $html.= '<div style="page-break-after:always">Second Tag</div>';
        $html.= '<p>Third Tag</p>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $result = $object->output('test.pdf', 'S');

        $this->assertNotEmpty($result);
        $this->assertSame(2, $object->getNbPages());
    }


    /**
     * test Break before and After
     *
     * @return void
     */
    public function testBreakBeforeAndAfter()
    {
        $html = '<p>First Tag</p>';
        $html.= '<div style="page-break-before:always; page-break-after:always">Second Tag</div>';
        $html.= '<p>Third Tag</p>';

        $object = $this->getObject();
        $object->writeHTML($html);
        $result = $object->output('test.pdf', 'S');

        $this->assertNotEmpty($result);
        $this->assertSame(3, $object->getNbPages());
    }
}
