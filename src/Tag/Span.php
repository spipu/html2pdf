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
 * Tag Span
 */
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
     * {@inheritDoc}
     */
    public function close(Node $node)
    {
        $this->parsingCss->restorePosition();

        return parent::close($node);
    }
}
