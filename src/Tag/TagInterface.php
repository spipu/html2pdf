<?php
/**
 * Html2Pdf Library - TagInterface interface
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tag;

use Spipu\Html2Pdf\CssConverter;
use Spipu\Html2Pdf\MyPdf;
use Spipu\Html2Pdf\Debug\DebugInterface;
use Spipu\Html2Pdf\Parsing\Css as ParsingCss;

interface TagInterface
{
    /**
     * PHP constructor.
     *
     * @return TagInterface
     */
    public function __construct();

    /**
     * Set the Parsing Css Object
     *
     * @param ParsingCss $parsingCss The parsing css object
     *
     * @return TagInterface
     */
    public function setParsingCssObject(ParsingCss $parsingCss);

    /**
     * Set the Parsing Css Object
     *
     * @param CssConverter $cssConverter The css converter object
     *
     * @return TagInterface
     */
    public function setCssConverterObject(CssConverter $cssConverter);

    /**
     * Set the Pdf Object
     *
     * @param MyPdf $pdf The pdf object
     *
     * @return TagInterface
     */
    public function setPdfObject(MyPdf $pdf);

    /**
     * Set the Debug Object
     *
     * @param DebugInterface $debug The Debug object
     *
     * @return TagInterface
     */
    public function setDebugObject(DebugInterface $debug);

    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName();

    /**
     * Open the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function open($properties);

    /**
     * Close the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function close($properties);
}
