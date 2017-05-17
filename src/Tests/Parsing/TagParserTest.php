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

namespace Spipu\Html2Pdf\Tests\Parsing;

use Phake;
use Spipu\Html2Pdf\Parsing\TagParser;

/**
 * Class TagParserTest
 */
class TagParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TagParser
     */
    private $parser;

    protected function setUp()
    {
        $textParser = Phake::mock('Spipu\Html2Pdf\Parsing\TextParser');
        $this->parser = new TagParser($textParser);
    }

    /**
     * @param string $code
     * @param array  $expected
     *
     * @dataProvider tagAttributesProvider
     */
    public function testExtractTagAttributes($code, $expected)
    {
        $result = $this->parser->extractTagAttributes($code);

        $this->assertEquals($expected, $result);
    }

    public function tagAttributesProvider()
    {
        return array(
            array('attr=value', array('attr' => 'value')),
            array('attr="value"', array('attr' => 'value')),
            array('attr=\'value\'', array('attr' => 'value')),
            array('attr="value with spaces"', array('attr' => 'value with spaces')),
            array('attr="value with \'quotes"', array('attr' => 'value with \'quotes')),
            array('my attr="value"', array('attr' => 'value')),
            array('attr1=val1 attr2="value2"', array('attr1' => 'val1', 'attr2' => 'value2')),
        );
    }
}
