<?php
/**
 * Html2Pdf Library - Tag B
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tag;

use Spipu\Html2Pdf\TagAbstract;

class DefaultTag extends TagAbstract
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
        $this->_parsingCss->save();
        $this->_overrideStyles();
        $this->_parsingCss->analyse($this->_tagName, $properties);
        $this->_parsingCss->setPosition();
        $this->_parsingCss->fontSet();

        return true;
    }

    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
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
        $this->_parsingCss->load();
        $this->_parsingCss->fontSet();
    }
}
