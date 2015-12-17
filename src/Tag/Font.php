<?php
/**
 * Html2Pdf Library - Tag Font
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class Font extends Span
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'font';
    }

}
