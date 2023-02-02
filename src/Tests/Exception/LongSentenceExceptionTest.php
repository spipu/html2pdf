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

namespace Spipu\Html2Pdf\Tests\Exception;

use Spipu\Html2Pdf\Exception\LongSentenceException;
use Spipu\Html2Pdf\Tests\AbstractTest;

/**
 * Class DebugTest
 */
class LongSentenceExceptionTest extends AbstractTest
{
    /**
     * test LongSentence Exception
     *
     * @return void
     */
    public function testBug()
    {
        $this->expectException(LongSentenceException::class);

        $sentence = 'This is a sentence.';
        $bigSentence = $sentence;
        for ($k=0; $k<110; $k++) {
            $bigSentence.= ' '.$sentence;
        }
        $html = '<page backleft="0" backright="200mm"style="font-size: 1mm">'.$bigSentence.'</page>';

        $object = $this->getObject();
        $object->setSentenceMaxLines(100);
        $object->writeHTML($html);
        $object->output('test.pdf', 'S');
    }
}
