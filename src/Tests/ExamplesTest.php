<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Tag;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class ExamplesTest
 *
 * @package   Html2pdf
 * @copyright 2016 Laurent MINGUET
 */
class ExamplesTest extends \PHPUnit_Framework_TestCase
{
    protected function _launchExample($example)
    {
        $filename = dirname(dirname(dirname(__FILE__))).'/examples/'.$example.'.php';
        if (!is_file($filename)) {
            throw new \Exception('The filename of the example ['.$example.'] does not exist!');
        }
        $folder = dirname($filename);

        // get the content of the file
        $content = file_get_contents($filename);

        // keep only the example
        $content = explode('try {', $content)[1];
        $content = explode('} catch', $content)[0];

        // replace the good path
        $content = str_replace('dirname(__FILE__)', "'$folder'", $content);

        // add the class to use
        $content = 'use Spipu\Html2Pdf\Html2Pdf; '.$content;

        // get the output
        $regexp = '/\$html2pdf->Output\(([^\)]*)\);/';
        $replace = 'return $html2pdf->Output(\'test.pdf\', \'S\');';
        $content = preg_replace($regexp, $replace, $content);

        // execute
        $currentDir = getcwd();
        chdir($folder);
        $result = eval($content);
        chdir($currentDir);

        // test
        $this->assertNotEmpty($result);
    }

    /**
     * test: about
     *
     * @return void
     */
    public function testAbout()
    {
        $this->_launchExample('about');
    }

    /**
     * test: bookmark
     *
     * @return void
     */
    public function testBookmark()
    {
        $this->_launchExample('bookmark');
    }

    /**
     * test: exemple01
     *
     * @return void
     */
    public function testExemple01()
    {
        $this->_launchExample('exemple01');
    }

    /**
     * test: exemple02
     *
     * @return void
     */
    public function testExemple02()
    {
        $this->_launchExample('exemple02');
    }

    /**
     * test: exemple03
     *
     * @return void
     */
    public function testExemple03()
    {
        $this->_launchExample('exemple03');
    }

    /**
     * test: exemple04
     *
     * @return void
     */
    public function testExemple04()
    {
        $this->_launchExample('exemple04');
    }

    /**
     * test: exemple05
     *
     * @return void
     */
    public function testExemple05()
    {
        $this->_launchExample('exemple05');
    }

    /**
     * test: exemple06
     *
     * @return void
     */
    public function testExemple06()
    {
        $this->_launchExample('exemple06');
    }

    /**
     * test: exemple07
     *
     * @return void
     */
    public function testExemple07()
    {
        $this->_launchExample('exemple07');
    }

    /**
     * test: exemple08
     *
     * @return void
     */
    public function testExemple08()
    {
        $this->_launchExample('exemple08');
    }

    /**
     * test: exemple10
     *
     * @return void
     */
    public function testExemple10()
    {
        $this->_launchExample('exemple10');
    }

    /**
     * test: exemple11
     *
     * @return void
     */
    public function testExemple11()
    {
        $this->_launchExample('exemple11');
    }

    /**
     * test: exemple12
     *
     * @return void
     */
    public function testExemple12()
    {
        $this->_launchExample('exemple12');
    }

    /**
     * test: exemple13
     *
     * @return void
     */
    public function testExemple13()
    {
        $this->_launchExample('exemple13');
    }

    /**
     * test: forms
     *
     * @return void
     */
    public function testForms()
    {
        $this->_launchExample('forms');
    }

    /**
     * test: groups
     *
     * @return void
     */
    public function testGroups()
    {
        $this->_launchExample('groups');
    }

    /**
     * test: js1
     *
     * @return void
     */
    public function testJs1()
    {
        $this->_launchExample('js1');
    }

    /**
     * test: js2
     *
     * @return void
     */
    public function testJs2()
    {
        $this->_launchExample('js2');
    }

    /**
     * test: js3
     *
     * @return void
     */
    public function testJs3()
    {
        $this->_launchExample('js3');
    }

    /**
     * test: qrcode
     *
     * @return void
     */
    public function testQrcode()
    {
        $this->_launchExample('qrcode');
    }

    /**
     * test: radius
     *
     * @return void
     */
    public function testRadius()
    {
        $this->_launchExample('radius');
    }

    /**
     * test: regle
     *
     * @return void
     */
    public function testRegle()
    {
        $this->_launchExample('regle');
    }

    /**
     * test: svg
     *
     * @return void
     */
    public function testSvg()
    {
        $this->_launchExample('svg');
    }

    /**
     * test: svg_tiger
     *
     * @return void
     */
    public function testSvgTiger()
    {
        $this->_launchExample('svg_tiger');
    }

    /**
     * test: svg_tree
     *
     * @return void
     */
    public function testSvgTree()
    {
        $this->_launchExample('svg_tree');
    }

    /**
     * test: ticket
     *
     * @return void
     */
    public function testTicket()
    {
        $this->_launchExample('ticket');
    }

    /**
     * test: utf8
     *
     * @return void
     */
    public function testUtf8()
    {
        $this->_launchExample('utf8');
    }
}
