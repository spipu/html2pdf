<?php
/**
 * Html2Pdf Library - Tests
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tests\Parsing;

use PHPUnit_Framework_TestCase;
use Spipu\Html2Pdf\Parsing\Token;

/**
 * Class TokenTest
 */
class TokenTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test if it works
     */
    public function testOk()
    {
        $token = new Token('hello', 'world', 45);
        $this->assertSame('hello', $token->getType());
        $this->assertSame('world', $token->getData());
        $this->assertSame(45, $token->getLine());
    }
}
