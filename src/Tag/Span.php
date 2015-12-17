<?php
/**
 * Html2Pdf Library - Tag Span
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class Span extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'span';
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
        $this->_parsingCss->restorePosition();

        return parent::close($properties);
    }
}
