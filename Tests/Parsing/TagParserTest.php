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

namespace Spipu\Html2Pdf\Tests\Parsing;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Parsing\Node;
use Spipu\Html2Pdf\Tests\CrossVersionCompatibility\TagParserTestCase;

/**
 * Class TagParserTest
 */
class TagParserTest extends TagParserTestCase
{
    /**
     * mock of prepareTxt method
     *
     * @param $txt
     * @param bool $spaces
     * @return mixed
     */
    public function mockPrepareTxt($txt, $spaces = true)
    {
        return $txt;
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

    /**
     * @return array
     */
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

    /**
     * Test if a bad tag is detected
     */
    public function testAnalyzeTagBadTag()
    {
        $this->expectException(HtmlParsingException::class);
        $this->parser->analyzeTag('test');
    }

    /**
     * Test basic open, close, autoclose tags
     */
    public function testBasicTags()
    {
        $result = $this->parser->analyzeTag('<my_tag/>');

        $this->assertTrue($result instanceof Node);
        $this->assertSame('my_tag', $result->getName());
        $this->assertSame(true, $result->isAutoClose());
        $this->assertSame(false, $result->isClose());

        $result->setLine(10);
        $this->assertSame(10, $result->getLine());

        $result->setParam('attr', 'value');
        $this->assertSame('value', $result->getParam('attr'));


        $result = $this->parser->analyzeTag('<my_tag>');

        $this->assertSame('my_tag', $result->getName());
        $this->assertSame(false, $result->isAutoClose());
        $this->assertSame(false, $result->isClose());
        $this->assertSame(['style' => [], 'num' => 0], $result->getParams());
        $this->assertSame('default', $result->getParam('attr', 'default'));

        $result = $this->parser->analyzeTag('</my_tag>');

        $this->assertSame('my_tag', $result->getName());
        $this->assertSame(false, $result->isAutoClose());
        $this->assertSame(true, $result->isClose());

    }

    /**
     * Test styles
     */
    public function testAnalyzeAttributes()
    {
        $result = $this->parser->analyzeTag('<img src="my_src" alt="my alt"/>');
        $this->assertSame('my_src', $result->getParam('src'));
        $this->assertSame('my alt', $result->getParam('alt'));

        $result = $this->parser->analyzeTag('<a href="my_src" title="my title"/>');
        $this->assertSame('my_src', $result->getParam('href'));
        $this->assertSame('my title', $result->getParam('title'));


        $result = $this->parser->analyzeTag('<input type="text" value="my value" class="my_class" />');
        $this->assertSame('text', $result->getParam('type'));
        $this->assertSame('my value', $result->getParam('value'));
        $this->assertSame('my_class', $result->getParam('class'));

        $result = $this->parser->analyzeTag('<my_tag width="10" height="20" align="center" valign="bottom" bgcolor="red">');
        $this->assertSame('10px', $result->getStyle('width'));
        $this->assertSame('20px', $result->getStyle('height'));
        $this->assertSame('center', $result->getStyle('text-align'));
        $this->assertSame('bottom', $result->getStyle('vertical-align'));
        $this->assertSame('red', $result->getStyle('background'));

        $result = $this->parser->analyzeTag('<img align="right">');
        $this->assertSame('right', $result->getStyle('float'));

        $result = $this->parser->analyzeTag('<table cellpadding="1" cellspacing="2">');
        $this->assertSame('1px', $result->getParam('cellpadding'));
        $this->assertSame('2px', $result->getParam('cellspacing'));

        $result = $this->parser->analyzeTag('<td rowspan="0" colspan="2px">');
        $this->assertSame(1, $result->getParam('rowspan'));
        $this->assertSame(2, $result->getParam('colspan'));

        $result = $this->parser->analyzeTag('<my_tag color="red">');
        $this->assertSame('red', $result->getParam('color'));
        $this->assertSame(null, $result->getStyle('color'));

        $result = $this->parser->analyzeTag('<font color="red">');
        $this->assertSame(null, $result->getParam('color'));
        $this->assertSame('red', $result->getStyle('color'));
    }

    /**
     * Test borders
     */
    public function testBorders()
    {
        $result = $this->parser->analyzeTag('<div border="1" bordercolor="red" />');

        $this->assertSame('div', $result->getName());
        $this->assertSame('solid 1px red', $result->getParam('border'));
        $this->assertSame('solid 1px red', $result->getStyle('border'));

        $result = $this->parser->analyzeTag('<div border="0" bordercolor="red" />');

        $this->assertSame('div', $result->getName());
        $this->assertSame('none', $result->getParam('border'));
        $this->assertSame('none', $result->getStyle('border'));
    }

    /**
     * Test levels
     */
    public function testLevels()
    {
        $result = $this->parser->analyzeTag('<basic_tag>');
        $this->assertSame(0, $result->getParam('num'));

        $result = $this->parser->analyzeTag('<table>');
        $this->assertSame(1, $result->getParam('num'));

        $result = $this->parser->analyzeTag('<ol>');
        $this->assertSame(2, $result->getParam('num'));

        $result = $this->parser->analyzeTag('<ul>');
        $this->assertSame(3, $result->getParam('num'));

        $result = $this->parser->analyzeTag('</ul>');
        $this->assertSame('ul', $result->getName());
        $this->assertSame(3, $result->getParam('num'));

        $result = $this->parser->analyzeTag('</ol>');
        $this->assertSame(2, $result->getParam('num'));

        $result = $this->parser->analyzeTag('</table>');
        $this->assertSame(1, $result->getParam('num'));

        $result = $this->parser->analyzeTag('<basic_tag>');
        $this->assertSame(0, $result->getParam('num'));
    }
}
