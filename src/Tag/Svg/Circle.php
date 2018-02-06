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
namespace Spipu\Html2Pdf\Tag\Svg;

use Spipu\Html2Pdf\Tag\AbstractSvgTag;

/**
 * Tag Circle
 */
class Circle extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'circle';
    }

    /**
     * @inheritdoc
     */
    protected function draw($properties)
    {
        $this->pdf->doTransform(
            isset($properties['transform'])
                ? $this->svgDrawer->prepareTransform($properties['transform'])
                : null
        );

        $this->parsingCss->save();
        $styles = $this->parsingCss->getSvgStyle('circle', $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $cx = isset($properties['cx']) ? $this->cssConverter->convertToMM($properties['cx'], $this->svgDrawer->getProperty('w')) : 0.;
        $cy = isset($properties['cy']) ? $this->cssConverter->convertToMM($properties['cy'], $this->svgDrawer->getProperty('h')) : 0.;
        $r  = isset($properties['r'])  ? $this->cssConverter->convertToMM($properties['r'], $this->svgDrawer->getProperty('w'))  : 0.;
        $this->pdf->svgEllipse($cx, $cy, $r, $r, $style);

        $this->pdf->undoTransform();
        $this->parsingCss->load();
    }
}
