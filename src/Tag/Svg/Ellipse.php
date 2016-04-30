<?php

namespace Spipu\Html2Pdf\Tag\Svg;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Parsing\Node;

/**
 * Class Ellipse
 */
class Ellipse extends SvgTag
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ellipse';
    }

    /**
     * {@inheritDoc}
     */
    public function open(Node $node)
    {
        if (!$this->svgDrawer->isInDraw()) {
            $e = new HtmlParsingException('The asked [ELLIPSE] tag is not in a [DRAW] tag');
            $e->setInvalidTag('ELLIPSE');
            throw $e;
        }

        $params = $node->getParams();

        //$this->pdf->doTransform(isset($param['transform']) ? $this->_prepareTransform($param['transform']) : null);
        $this->parsingCss->save();
        $styles = $this->parsingCss->getSvgStyle('path', $params);
        $this->svgDrawer->ellipse($params, $styles);

        //$this->pdf->undoTransform();
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
