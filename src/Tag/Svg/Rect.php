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
    protected function drawSvg($properties)
    {
        $styles = $this->parsingCss->getSvgStyle($this->getName(), $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $x = 0.;
        if (isset($properties['x'])) {
            $x = $this->cssConverter->convertToMM($properties['x'], $this->svgDrawer->getProperty('w'));
        }

        $y = 0.;
        if (isset($properties['y'])) {
            $y = $this->cssConverter->convertToMM($properties['y'], $this->svgDrawer->getProperty('h'));
        }

        $w = 0.;
        if (isset($properties['w'])) {
            $w = $this->cssConverter->convertToMM($properties['w'], $this->svgDrawer->getProperty('w'));
        }

        $h = 0.;
        if (isset($properties['h'])) {
            $h = $this->cssConverter->convertToMM($properties['h'], $this->svgDrawer->getProperty('h'));
        }

        $this->pdf->svgRect($x, $y, $w, $h, $style);
    }
}
