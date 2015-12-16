<?php
/**
 * Html2Pdf Library - Tag I
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class I extends DefaultTag
{
    /**
     * Tag name
     * @var string
     */
    protected $_tagName = 'i';

    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
    {
        $this->_parsingCss->value['font-italic'] = true;

        return $this;
    }
}