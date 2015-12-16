<?php
/**
 * Html2Pdf Library - Tag U
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class U extends DefaultTag
{
    /**
     * Tag name
     * @var string
     */
    protected $_tagName = 'u';

    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
    {
        $this->_parsingCss->value['font-underline'] = true;

        return $this;
    }
}
