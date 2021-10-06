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
 * Tag Path
 */
class Path extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'path';
    }

    /**
     * @inheritdoc
     */
    protected function drawSvg($properties)
    {
        $styles = $this->parsingCss->getSvgStyle($this->getName(), $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $path = isset($properties['d']) ? $properties['d'] : null;

        if ($path) {
            // prepare the path
            $path = str_replace(',', ' ', $path);
            $path = preg_replace('/([a-df-zA-DF-Z])([0-9\.\-])/', '$1 $2', $path);
            $path = preg_replace('/([0-9\.])([a-df-zA-DF-Z])/', '$1 $2', $path);
            $path = preg_replace('/([0-9\.])([-])([0-9\.])/', '$1 $2$3', $path);
            $path = preg_replace('/[\s]+/', ' ', trim($path));
            $path = preg_replace('/ ([a-z]{2})/', '$1', $path);
            $path = preg_replace('/Z([a-zA-Z])/', 'Z $1', $path);

            $path = explode(' ', $path);
            foreach ($path as $k => $v) {
                $path[$k] = trim($v);
                if ($path[$k] === '') {
                    unset($path[$k]);
                }
            }
            $path = array_values($path);
            $amountPath = count($path);

            // read each actions in the path
            $actions = array();
            $lastAction = null; // last action found
            for ($k=0; $k<$amountPath; true) {
                // for this actions, we can not have multi coordinate
                if (in_array($lastAction, array('z', 'Z'))) {
                    $lastAction = null;
                }

                // read the new action (forcing if no action before)
                if (preg_match('/^[a-z]+$/i', $path[$k]) || $lastAction === null) {
                    $lastAction = $path[$k];
                    $k++;
                }

                // current action
                $action = array();
                $action[] = $lastAction;
                switch ($lastAction) {
                    case 'C':
                    case 'c':
                        // x1 y1 x2 y2 x y
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+2], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+3], $this->svgDrawer->getProperty('h'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+4], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+5], $this->svgDrawer->getProperty('h'));
                        $k+= 6;
                        break;

                    case 'Q':
                    case 'S':
                    case 'q':
                    case 's':
                        // x2 y2 x y
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+2], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+3], $this->svgDrawer->getProperty('h'));
                        $k+= 4;
                        break;

                    case 'A':
                    case 'a':
                        // rx ry (angle de deviation de l'axe X) (large-arc-flag) (sweep-flag) x y
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));
                        $action[] = 1.*$path[$k+2];
                        $action[] = ($path[$k+3] === '1') ? 1 : 0;
                        $action[] = ($path[$k+4] === '1') ? 1 : 0;
                        $action[] = $this->cssConverter->convertToMM($path[$k+5], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+6], $this->svgDrawer->getProperty('h'));
                        $k+= 7;
                        break;

                    case 'M':
                    case 'L':
                    case 'T':
                    case 'm':
                    case 'l':
                    case 't':
                        // x y
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));
                        $k+= 2;
                        break;

                    case 'H':
                    case 'h':
                        // x
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));
                        $k+= 1;
                        break;

                    case 'V':
                    case 'v':
                        // y
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('h'));
                        $k+= 1;
                        break;

                    case 'z':
                    case 'Z':
                        break;

                    default:
                        $k+= 1;
                        break;
                }
                // add the action
                $actions[] = $action;
            }

            // drawing
            $this->pdf->svgPolygone($actions, $style);
        }
    }
}
