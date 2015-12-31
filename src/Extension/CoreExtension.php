<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Extension;

/**
 * Class CoreExtension
 */
class CoreExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    private $tagDefinitions = array();

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core';
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        if (empty($this->tagDefinitions)) {
            $this->tagDefinitions = array(
                new \Spipu\Html2Pdf\Tag\Address(),
                new \Spipu\Html2Pdf\Tag\B(),
                new \Spipu\Html2Pdf\Tag\Big(),
                new \Spipu\Html2Pdf\Tag\Bookmark(),
                new \Spipu\Html2Pdf\Tag\Cite(),
                new \Spipu\Html2Pdf\Tag\Del(),
                new \Spipu\Html2Pdf\Tag\Em(),
                new \Spipu\Html2Pdf\Tag\Font(),
                new \Spipu\Html2Pdf\Tag\I(),
                new \Spipu\Html2Pdf\Tag\Ins(),
                new \Spipu\Html2Pdf\Tag\Label(),
                new \Spipu\Html2Pdf\Tag\S(),
                new \Spipu\Html2Pdf\Tag\Samp(),
                new \Spipu\Html2Pdf\Tag\Small(),
                new \Spipu\Html2Pdf\Tag\Span(),
                new \Spipu\Html2Pdf\Tag\Strong(),
                new \Spipu\Html2Pdf\Tag\Sub(),
                new \Spipu\Html2Pdf\Tag\Sup(),
                new \Spipu\Html2Pdf\Tag\U(),
            );
        }

        return $this->tagDefinitions;
    }
}
