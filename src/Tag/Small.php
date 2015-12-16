<?php
/**
 * Html2Pdf Library - Tag Small
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class Small extends DefaultTag
{
    /**
     * Tag name
     * @var string
     */
    protected $_tagName = 'small';

    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
    {
        $this->_parsingCss->value['mini-decal']+= $this->_parsingCss->value['mini-size']*0.05;
        $this->_parsingCss->value['mini-size'] *= 0.82;

        return $this;
    }
}
