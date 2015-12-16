<?php
/**
 * Html2Pdf Library - TagInterface interface
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf;

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
     * @param Parsing\Css $parsingCss The parsing css object
     *
     * @return TagAbstract
     * @throws \Exception
     */
    public function setParsingCssObject(Parsing\Css $parsingCss);

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