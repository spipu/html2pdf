<?php

namespace Spipu\Html2Pdf\Tag\Svg;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Parsing\Node;

/**
 * Class Line
 */
class Line extends SvgTag
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'line';
    }

    /**
     * {@inheritDoc}
     */
    public function open(Node $node)
    {
        if (!$this->svgDrawer->isInDraw()) {
            $e = new HtmlParsingException('The asked [LINE] tag is not in a [DRAW] tag');
            $e->setInvalidTag('LINE');
            throw $e;
        }

        $params = $node->getParams();
        $this->pdf->doTransform(isset($params['transform']) ? $this->svgDrawer->prepareTransform($params['transform']) : null);
        $this->parsingCss->save();
        $styles = $this->parsingCss->getSvgStyle('path', $params);
        $styles['fill'] = null;
        $this->svgDrawer->line($params, $styles);

        $this->pdf->undoTransform();
        $this->parsingCss->load();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(Node $node)
    {
        return true;
    }
}
