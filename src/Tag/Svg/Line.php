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
 * Tag Line
 */
class Line extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'line';
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
        $styles['fill'] = null;
        $style = $this->pdf->svgSetStyle($styles);

        $x1 = isset($properties['x1']) ? $this->cssConverter->convertToMM($properties['x1'], $this->svgDrawer->getProperty('w')) : 0.;
        $y1 = isset($properties['y1']) ? $this->cssConverter->convertToMM($properties['y1'], $this->svgDrawer->getProperty('h')) : 0.;
        $x2 = isset($properties['x2']) ? $this->cssConverter->convertToMM($properties['x2'], $this->svgDrawer->getProperty('w')) : 0.;
        $y2 = isset($properties['y2']) ? $this->cssConverter->convertToMM($properties['y2'], $this->svgDrawer->getProperty('h')) : 0.;
        $this->pdf->svgLine($x1, $y1, $x2, $y2);

        $this->pdf->undoTransform();
        $this->parsingCss->load();
    }
}
