<?php
/**
 * Html2Pdf Library - Abstract Tag class
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tag;

use Spipu\Html2Pdf\TagInterface;
use Spipu\Html2Pdf\Parsing\Css as ParsingCss;

abstract class AbstractTag implements TagInterface
{
    /**
     * Css Parsing object
     * @var ParsingCss
     */
    protected $_parsingCss;

    /**
     * PHP constructor.
     *
     * @return AbstractTag
     * @throws \Exception
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
     * @throws \Exception
     */
    public function setParsingCssObject(ParsingCss $parsingCss)
    {
        $this->_parsingCss = $parsingCss;

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
