<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

/**
 * Tag Cite
 */
class Cite extends I
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'cite';
    }
}
