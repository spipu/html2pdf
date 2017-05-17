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
        $titre = isset($properties['title']) ? trim($properties['title']) : '';
        $level = isset($properties['level']) ? floor($properties['level']) : 0;

        if ($level < 0) {
            $level = 0;
        }
        if ($titre) {
            $this->pdf->Bookmark($titre, $level, -1);
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
