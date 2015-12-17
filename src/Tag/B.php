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

class B extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'b';
    }

    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
    {
        $this->_parsingCss->value['font-bold'] = true;

        return $this;
    }
}
