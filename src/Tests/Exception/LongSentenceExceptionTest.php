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

use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class DebugTest
 */
class LongSentenceExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test LongSentence Exception
     *
     * @return void
     * @expectedException \Spipu\Html2Pdf\Exception\LongSentenceException
     */
    public function testBug()
    {
        $sentence = 'This is a sentence.';
        $bigSentence = $sentence;
        for ($k=0; $k<110; $k++) {
            $bigSentence.= ' '.$sentence;
        }
        $html = '<page backleft="0" backright="200mm"style="font-size: 1mm">'.$bigSentence.'</page>';

        $object = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', [0, 0, 0, 0]);
        $object->pdf->SetTitle('PhpUnit Test');
        $object->setSentenceMaxLines(100);
        $object->writeHTML($html);
        $object->output('test.pdf', 'S');
    }
}
