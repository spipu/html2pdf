<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tag;

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
     * PHP constructor.
     *
     * @return AbstractTag
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
     * get the name of the tag
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Open the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    abstract public function open($properties);

    /**
     * Close the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    abstract public function close($properties);
}
