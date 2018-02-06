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
    protected function draw($properties)
    {

        $this->pdf->doTransform(isset($properties['transform']) ? $this->svgDrawer->prepareTransform($properties['transform']) : null);
        $this->parsingCss->save();
        $styles = $this->parsingCss->getSvgStyle('path', $properties);
        $style = $this->pdf->svgSetStyle($styles);

        $path = isset($properties['d']) ? $properties['d'] : null;

        if ($path) {
            // prepare the path
            $path = str_replace(',', ' ', $path);
            $path = preg_replace('/([a-zA-Z])([0-9\.\-])/', '$1 $2', $path);
            $path = preg_replace('/([0-9\.])([a-zA-Z])/', '$1 $2', $path);
            $path = preg_replace('/[\s]+/', ' ', trim($path));
            $path = preg_replace('/ ([a-z]{2})/', '$1', $path);

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

                // for this actions, we can not have multi coordonate
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
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));    // x1
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));    // y1
                        $action[] = $this->cssConverter->convertToMM($path[$k+2], $this->svgDrawer->getProperty('w'));    // x2
                        $action[] = $this->cssConverter->convertToMM($path[$k+3], $this->svgDrawer->getProperty('h'));    // y2
                        $action[] = $this->cssConverter->convertToMM($path[$k+4], $this->svgDrawer->getProperty('w'));    // x
                        $action[] = $this->cssConverter->convertToMM($path[$k+5], $this->svgDrawer->getProperty('h'));    // y
                        $k+= 6;
                        break;

                    case 'Q':
                    case 'S':
                    case 'q':
                    case 's':
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));    // x2
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));    // y2
                        $action[] = $this->cssConverter->convertToMM($path[$k+2], $this->svgDrawer->getProperty('w'));    // x
                        $action[] = $this->cssConverter->convertToMM($path[$k+3], $this->svgDrawer->getProperty('h'));    // y
                        $k+= 4;
                        break;

                    case 'A':
                    case 'a':
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));    // rx
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));    // ry
                        $action[] = 1.*$path[$k+2];                                                        // angle de deviation de l'axe X
                        $action[] = ($path[$k+3] === '1') ? 1 : 0;                                            // large-arc-flag
                        $action[] = ($path[$k+4] === '1') ? 1 : 0;                                            // sweep-flag
                        $action[] = $this->cssConverter->convertToMM($path[$k+5], $this->svgDrawer->getProperty('w'));    // x
                        $action[] = $this->cssConverter->convertToMM($path[$k+6], $this->svgDrawer->getProperty('h'));    // y
                        $k+= 7;
                        break;

                    case 'M':
                    case 'L':
                    case 'T':
                    case 'm':
                    case 'l':
                    case 't':
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));    // x
                        $action[] = $this->cssConverter->convertToMM($path[$k+1], $this->svgDrawer->getProperty('h'));    // y
                        $k+= 2;
                        break;

                    case 'H':
                    case 'h':
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('w'));    // x
                        $k+= 1;
                        break;

                    case 'V':
                    case 'v':
                        $action[] = $this->cssConverter->convertToMM($path[$k+0], $this->svgDrawer->getProperty('h'));    // y
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

        $this->pdf->undoTransform();
        $this->parsingCss->load();
    }
}
