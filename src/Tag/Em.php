<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

/**
 * Tag Em
 */
class Em extends I
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'em';
    }
}
