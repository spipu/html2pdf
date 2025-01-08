<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

/**
 * Abstract Default Tag
 * used by all the simple tags like b, u, i, ...
 */
abstract class AbstractHtmlTag extends AbstractTag
{
    /**
     * Open the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function open($properties)
    {
        $this->parsingCss->save();
        $this->overrideStyles();
        $this->parsingCss->analyse($this->getName(), $properties);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * override some styles
     *
     * @return $this
     */
    protected function overrideStyles()
    {
        return $this;
    }

    /**
     * Close the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function close($properties)
    {
        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }
}
