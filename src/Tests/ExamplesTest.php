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

namespace Spipu\Html2Pdf\Tests;

if (HTML2PDF_PHPUNIT_VERSION === 9) {
    abstract class BaseExamplesTest extends \PHPUnit\Framework\TestCase
    {
    }
} else {
    abstract class BaseExamplesTest extends \PHPUnit_Framework_TestCase
    {
    }
}

/**
 * Class ExamplesTest
 */
class ExamplesTest extends BaseExamplesTest
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
     * test: bookmark
     *
     * @return void
     */
    public function testBalloon()
    {
        $this->launchExample('balloon');
    }

    /**
     * test: example01
     *
     * @return void
     */
    public function testExample01()
    {
        $this->launchExample('example01');
    }

    /**
     * test: example02
     *
     * @return void
     */
    public function testExample02()
    {
        $this->launchExample('example02');
    }

    /**
     * test: example03
     *
     * @return void
     */
    public function testExample03()
    {
        $this->launchExample('example03');
    }

    /**
     * test: example04
     *
     * @return void
     */
    public function testExample04()
    {
        $this->launchExample('example04');
    }

    /**
     * test: example05
     *
     * @return void
     */
    public function testExample05()
    {
        $this->launchExample('example05');
    }

    /**
     * test: example06
     *
     * @return void
     */
    public function testExample06()
    {
        $this->launchExample('example06');
    }

    /**
     * test: example07
     *
     * @return void
     */
    public function testExample07()
    {
        $this->launchExample('example07');
    }

    /**
     * test: example08
     *
     * @return void
     */
    public function testExample08()
    {
        $this->launchExample('example08');
    }

    /**
     * test: example10
     *
     * @return void
     */
    public function testExample10()
    {
        $this->launchExample('example10');
    }

    /**
     * test: example11
     *
     * @return void
     */
    public function testExample11()
    {
        $this->launchExample('example11');
    }

    /**
     * test: example12
     *
     * @return void
     */
    public function testExample12()
    {
        $this->launchExample('example12');
    }

    /**
     * test: example13
     *
     * @return void
     */
    public function testExample13()
    {
        $this->launchExample('example13');
    }

    /**
     * test: example14
     *
     * @return void
     */
    public function testExample14()
    {
        $this->launchExample('example14');
    }

    /**
     * test: example15
     *
     * @return void
     */
    public function testExample15()
    {
        $this->launchExample('example15');
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
    public function testMeasure()
    {
        $this->launchExample('measure');
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
