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
