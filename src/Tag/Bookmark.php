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
 * Tag Bookmark
 */
class Bookmark extends AbstractTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'bookmark';
    }

    /**
     * Open the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function open($properties)
    {
        $title = isset($properties['title']) ? trim($properties['title']) : '';
        $level = isset($properties['level']) ? floor($properties['level']) : 0;

        if ($level < 0) {
            $level = 0;
        }
        if ($title) {
            $this->pdf->Bookmark($title, $level, -1);
        }

        return true;
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
        // there is nothing to do here

        return true;
    }
}
