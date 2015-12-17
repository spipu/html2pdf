<?php
/**
 * Html2Pdf Library - Tag Sub
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class Sub extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'sub';
    }


    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
    {
        $this->_parsingCss->value['mini-decal']+= $this->_parsingCss->value['mini-size']*0.15;
        $this->_parsingCss->value['mini-size'] *= 0.75;

        return $this;
    }
}
