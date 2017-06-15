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

/**
 * Class ExamplesTest
 */
class ExamplesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Launch a example
     *
     * @param string $example code of the example
     *
     * @return void
     * @throws \Exception
     */
    protected function launchExample($example)
    {
        $filename = dirname(dirname(dirname(__FILE__))).'/examples/'.$example.'.php';
        if (!is_file($filename)) {
            throw new \Exception('The filename of the example ['.$example.'] does not exist!');
        }
        $folder = dirname($filename);

        // get the content of the file
        $content = file_get_contents($filename);

        // keep only the example
        $parts = explode('try {', $content);
        $parts = explode('} catch', $parts[1]);
        $content = $parts[0];

        // replace the good path
        $content = str_replace('dirname(__FILE__)', "'$folder'", $content);

        // add the class to use
        $content = 'use Spipu\Html2Pdf\Html2Pdf; '.$content;

        // get the output
        $regexp = '/\$html2pdf->output\(([^\)]*)\);/';
        $replace = 'return $html2pdf->output(\'test.pdf\', \'S\');';
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
        $this->launchExample('about');
    }

    /**
     * test: bookmark
     *
     * @return void
     */
    public function testBookmark()
    {
        $this->launchExample('bookmark');
    }

    /**
     * test: exemple01
     *
     * @return void
     */
    public function testExemple01()
    {
        $this->launchExample('exemple01');
    }

    /**
     * test: exemple02
     *
     * @return void
     */
    public function testExemple02()
    {
        $this->launchExample('exemple02');
    }

    /**
     * test: exemple03
     *
     * @return void
     */
    public function testExemple03()
    {
        $this->launchExample('exemple03');
    }

    /**
     * test: exemple04
     *
     * @return void
     */
    public function testExemple04()
    {
        $this->launchExample('exemple04');
    }

    /**
     * test: exemple05
     *
     * @return void
     */
    public function testExemple05()
    {
        $this->launchExample('exemple05');
    }

    /**
     * test: exemple06
     *
     * @return void
     */
    public function testExemple06()
    {
        $this->launchExample('exemple06');
    }

    /**
     * test: exemple07
     *
     * @return void
     */
    public function testExemple07()
    {
        $this->launchExample('exemple07');
    }

    /**
     * test: exemple08
     *
     * @return void
     */
    public function testExemple08()
    {
        $this->launchExample('exemple08');
    }

    /**
     * test: exemple10
     *
     * @return void
     */
    public function testExemple10()
    {
        $this->launchExample('exemple10');
    }

    /**
     * test: exemple11
     *
     * @return void
     */
    public function testExemple11()
    {
        $this->launchExample('exemple11');
    }

    /**
     * test: exemple12
     *
     * @return void
     */
    public function testExemple12()
    {
        $this->launchExample('exemple12');
    }

    /**
     * test: exemple13
     *
     * @return void
     */
    public function testExemple13()
    {
        $this->launchExample('exemple13');
    }

    /**
     * test: exemple14
     *
     * @return void
     */
    public function testExemple14()
    {
        $this->launchExample('exemple14');
    }

    /**
     * test: forms
     *
     * @return void
     */
    public function testForms()
    {
        $this->launchExample('forms');
    }

    /**
     * test: groups
     *
     * @return void
     */
    public function testGroups()
    {
        $this->launchExample('groups');
    }

    /**
     * test: js1
     *
     * @return void
     */
    public function testJs1()
    {
        $this->launchExample('js1');
    }

    /**
     * test: js2
     *
     * @return void
     */
    public function testJs2()
    {
        $this->launchExample('js2');
    }

    /**
     * test: js3
     *
     * @return void
     */
    public function testJs3()
    {
        $this->launchExample('js3');
    }

    /**
     * test: qrcode
     *
     * @return void
     */
    public function testQrcode()
    {
        $this->launchExample('qrcode');
    }

    /**
     * test: radius
     *
     * @return void
     */
    public function testRadius()
    {
        $this->launchExample('radius');
    }

    /**
     * test: regle
     *
     * @return void
     */
    public function testRegle()
    {
        $this->launchExample('regle');
    }

    /**
     * test: svg
     *
     * @return void
     */
    public function testSvg()
    {
        $this->launchExample('svg');
    }

    /**
     * test: svg_tiger
     *
     * @return void
     */
    public function testSvgTiger()
    {
        $this->launchExample('svg_tiger');
    }

    /**
     * test: svg_tree
     *
     * @return void
     */
    public function testSvgTree()
    {
        $this->launchExample('svg_tree');
    }

    /**
     * test: ticket
     *
     * @return void
     */
    public function testTicket()
    {
        $this->launchExample('ticket');
    }

    /**
     * test: utf8
     *
     * @return void
     */
    public function testUtf8()
    {
        $this->launchExample('utf8');
    }
}
