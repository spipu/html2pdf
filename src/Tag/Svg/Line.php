<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
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
    protected function drawSvg($properties)
    {
        $styles = $this->parsingCss->getSvgStyle($this->getName(), $properties);
        $styles['fill'] = null;
        $this->pdf->svgSetStyle($styles);

        $x1 = 0.;
        if (isset($properties['x1'])) {
            $x1 = $this->cssConverter->convertToMM($properties['x1'], $this->svgDrawer->getProperty('w'));
        }

        $y1 = 0.;
        if (isset($properties['y1'])) {
            $y1 = $this->cssConverter->convertToMM($properties['y1'], $this->svgDrawer->getProperty('h'));
        }

        $x2 = 0.;
        if (isset($properties['x2'])) {
            $x2 = $this->cssConverter->convertToMM($properties['x2'], $this->svgDrawer->getProperty('w'));
        }

        $y2 = 0.;
        if (isset($properties['y2'])) {
            $y2 = $this->cssConverter->convertToMM($properties['y2'], $this->svgDrawer->getProperty('h'));
        }

        $this->pdf->svgLine($x1, $y1, $x2, $y2);
    }
}
