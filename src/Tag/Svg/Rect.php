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
 * Tag Rect
 */
class Rect extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'rect';
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
        $styles = $this->parsingCss->getSvgStyle('path', $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $x = isset($properties['x']) ? $this->cssConverter->convertToMM($properties['x'], $this->svgDrawer->getProperty('w')) : 0.;
        $y = isset($properties['y']) ? $this->cssConverter->convertToMM($properties['y'], $this->svgDrawer->getProperty('h')) : 0.;
        $w = isset($properties['w']) ? $this->cssConverter->convertToMM($properties['w'], $this->svgDrawer->getProperty('w')) : 0.;
        $h = isset($properties['h']) ? $this->cssConverter->convertToMM($properties['h'], $this->svgDrawer->getProperty('h')) : 0.;

        $this->pdf->svgRect($x, $y, $w, $h, $style);

        $this->pdf->undoTransform();
        $this->parsingCss->load();
    }
}
