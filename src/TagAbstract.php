<?php
/**
 * Html2Pdf Library - TagAbstrat class
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf;

abstract class TagAbstract implements TagInterface
{
    /**
     * Tag name, must be defined in each tag classe
     * @var string
     */
    protected $_tagName;

    /**
     * Css Parsing object
     * @var Parsing\Css
     */
    protected $_parsingCss;

    /**
     * PHP constructor.
     *
     * @return TagAbstract
     * @throws \Exception
     */
    public function __construct()
    {
        if (is_null($this->_tagName)) {
            throw new \Exception('Tag name is not defined');
        }
    }

    /**
     * Set the Parsing Css Object
     *
     * @param Parsing\Css $parsingCss The parsing css object
     *
     * @return TagAbstract
     * @throws \Exception
     */
    public function setParsingCssObject(Parsing\Css $parsingCss)
    {
        $this->_parsingCss = $parsingCss;

        return $this;
    }

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
