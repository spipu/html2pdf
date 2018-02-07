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
 * Tag Polyline
 */
class Polyline extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'polyline';
    }

    /**
     * @inheritdoc
     */
    protected function drawSvg($properties)
    {
        $styles = $this->parsingCss->getSvgStyle($this->getName(), $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $path = isset($properties['points']) ? $properties['points'] : null;
        if ($path) {
            $path = str_replace(',', ' ', $path);
            $path = preg_replace('/[\s]+/', ' ', trim($path));

            // prepare the path
            $path = explode(' ', $path);
            foreach ($path as $k => $v) {
                $path[$k] = trim($v);
                if ($path[$k] === '') {
                    unset($path[$k]);
                }
            }
            $path = array_values($path);

            $amountPath = count($path);

            $actions = array();
            for ($k=0; $k<$amountPath; $k+=2) {
                $actions[] = array(
                    ($k ? 'L' : 'M') ,
                    $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w')),
                    $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'))
                );
            }

            // drawing
            $this->pdf->svgPolygone($actions, $style);
        }
    }
}
