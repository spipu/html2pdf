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
 * Abstract Default Tag
 * used by all the simple tags like b, u, i, ...
 */
abstract class AbstractDefaultTag extends AbstractTag
{
    /**
     * {@inheritDoc}
     */
    public function open(Node $node)
    {
        $this->parsingCss->save();
        $this->overrideStyles();
        $this->parsingCss->analyse($this->getName(), $node->getParams());
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * override some styles
     *
     * @return Span
     */
    protected function overrideStyles()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function close(Node $node)
    {
        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }
}
