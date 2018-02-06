<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tag;

use Spipu\Html2Pdf\CssConverter;
use Spipu\Html2Pdf\MyPdf;
use Spipu\Html2Pdf\Debug\DebugInterface;
use Spipu\Html2Pdf\Parsing\Css as ParsingCss;

/**
 * Abstract Tag
 * must be used by all the tags
 */
abstract class AbstractTag implements TagInterface
{
    /**
     * Css Parsing object
     * @var ParsingCss
     */
    protected $parsingCss;

    /**
     * Css Converter object
     * @var CssConverter
     */
    protected $cssConverter;

    /**
     * Pdf object
     * @var MyPdf
     */
    protected $pdf;

    /**
     * Debug object
     * @var DebugInterface
     */
    protected $debug;

    /**
     * PHP constructor.
     */
    public function __construct()
    {

    }

    /**
     * Set the Parsing Css Object
     *
     * @param ParsingCss $parsingCss The parsing css object
     *
     * @return AbstractTag
     */
    public function setParsingCssObject(ParsingCss $parsingCss)
    {
        $this->parsingCss = $parsingCss;

        return $this;
    }

    /**
     * Set the Parsing Css Object
     *
     * @param CssConverter $cssConverter The css converter object
     *
     * @return AbstractTag
     */
    public function setCssConverterObject(CssConverter $cssConverter)
    {
        $this->cssConverter = $cssConverter;

        return $this;
    }

    /**
     * Set the Pdf Object
     *
     * @param MyPdf $pdf The pdf object
     *
     * @return TagInterface
     */
    public function setPdfObject(MyPdf $pdf)
    {
        $this->pdf = $pdf;

        return $this;
    }

    /**
     * Set the Debug Object
     *
     * @param DebugInterface $debug The Debug object
     *
     * @return TagInterface
     */
    public function setDebugObject(DebugInterface $debug)
    {
        $this->debug = $debug;

        return $this;
    }
}
