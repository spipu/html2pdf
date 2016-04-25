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

use Spipu\Html2Pdf\Parsing\Node;

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
     * {@inheritDoc}
     */
    public function open(Node $node)
    {
        $properties = $node->getParams();
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
     * {@inheritDoc}
     */
    public function close(Node $node)
    {
        // there is nothing to do here

        return true;
    }
}
