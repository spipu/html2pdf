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
    protected function drawSvg($properties)
    {
        $styles = $this->parsingCss->getSvgStyle($this->getName(), $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $cx = 0.;
        if (isset($properties['cx'])) {
            $cx = $this->cssConverter->convertToMM($properties['cx'], $this->svgDrawer->getProperty('w'));
        }

        $cy = 0.;
        if (isset($properties['cy'])) {
            $cy = $this->cssConverter->convertToMM($properties['cy'], $this->svgDrawer->getProperty('h'));
        }

        $r = 0.;
        if (isset($properties['r'])) {
            $r = $this->cssConverter->convertToMM($properties['r'], $this->svgDrawer->getProperty('w'));
        }

        $this->pdf->svgEllipse($cx, $cy, $r, $r, $style);
    }
}
