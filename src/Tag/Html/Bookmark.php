<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag\Html;

use Spipu\Html2Pdf\Tag\AbstractTag;

/**
 * Tag Bookmark
 */
class Bookmark extends AbstractTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'bookmark';
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function close($properties)
    {
        // there is nothing to do here

        return true;
    }
}
