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
 * Tag Ellipse
 */
class Ellipse extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ellipse';
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

        $rx = 0.;
        if (isset($properties['rx'])) {
            $rx = $this->cssConverter->convertToMM($properties['rx'], $this->svgDrawer->getProperty('w'));
        }

        $ry = 0.;
        if (isset($properties['ry'])) {
            $ry = $this->cssConverter->convertToMM($properties['ry'], $this->svgDrawer->getProperty('h'));
        }

        $this->pdf->svgEllipse($cx, $cy, $rx, $ry, $style);
    }
}
