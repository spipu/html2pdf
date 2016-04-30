<?php 

namespace Spipu\Html2Pdf;

/**
 * Class SvgDrawer
 */
class SvgDrawer
{
    /**
     * @var bool
     */
    private $isDrawing = false;

    /**
     * @var array
     */
    private $coordinates;

    /**
     * @var MyPdf
     */
    private $pdf;

    /**
     * @var CssConverter
     */
    private $cssConverter;

    /**
     * @param $pdf
     * @param $cssConverter
     */
    public function __construct($pdf, $cssConverter)
    {
        $this->pdf = $pdf;
        $this->cssConverter = $cssConverter;
    }

    public function isInDraw()
    {
        return $this->isDrawing;
    }

    /**
     * @param array $coords Coordinates as array with x, y, w, h keys
     */
    public function startDrawing($coords)
    {
        $this->isDrawing = true;
        $this->coordinates = $coords;
    }

    /**
     * Stop drawing mode
     */
    public function stopDrawing()
    {
        $this->isDrawing = false;
    }

    /**
     * @param $params
     * @param $styles
     */
    public function ellipse($params, $styles)
    {
        $style = $this->pdf->svgSetStyle($styles);
        $cx = isset($params['cx']) ? $this->cssConverter->ConvertToMM($params['cx'], $this->coordinates['w']) : 0.;
        $cy = isset($params['cy']) ? $this->cssConverter->ConvertToMM($params['cy'], $this->coordinates['h']) : 0.;
        $rx = isset($params['ry']) ? $this->cssConverter->ConvertToMM($params['rx'], $this->coordinates['w']) : 0.;
        $ry = isset($params['rx']) ? $this->cssConverter->ConvertToMM($params['ry'], $this->coordinates['h']) : 0.;
        $this->pdf->svgEllipse($cx, $cy, $rx, $ry, $style);
    }

    /**
     * @param $params
     * @param $styles
     */
    public function circle($params, $styles)
    {
        $style = $this->pdf->svgSetStyle($styles);
        $cx = isset($params['cx']) ? $this->cssConverter->ConvertToMM($params['cx'], $this->coordinates['w']) : 0.;
        $cy = isset($params['cy']) ? $this->cssConverter->ConvertToMM($params['cy'], $this->coordinates['h']) : 0.;
        $r  = isset($params['r'])  ? $this->cssConverter->ConvertToMM($params['r'],  $this->coordinates['w']) : 0.;
        $this->pdf->svgEllipse($cx, $cy, $r, $r, $style);
    }

    /**
     * @param $params
     * @param $styles
     */
    public function line($params, $styles)
    {
        $this->pdf->svgSetStyle($styles);

        $x1 = isset($params['x1']) ? $this->cssConverter->ConvertToMM($params['x1'], $this->coordinates['w']) : 0.;
        $y1 = isset($params['y1']) ? $this->cssConverter->ConvertToMM($params['y1'], $this->coordinates['h']) : 0.;
        $x2 = isset($params['x2']) ? $this->cssConverter->ConvertToMM($params['x2'], $this->coordinates['w']) : 0.;
        $y2 = isset($params['y2']) ? $this->cssConverter->ConvertToMM($params['y2'], $this->coordinates['h']) : 0.;
        $this->pdf->svgLine($x1, $y1, $x2, $y2);
    }

    /**
     * @param $params
     * @param $styles
     */
    public function rectangle($params, $styles)
    {
        $style = $this->pdf->svgSetStyle($styles);

        $x = isset($params['x']) ? $this->cssConverter->ConvertToMM($params['x'], $this->coordinates['w']) : 0.;
        $y = isset($params['y']) ? $this->cssConverter->ConvertToMM($params['y'], $this->coordinates['h']) : 0.;
        $w = isset($params['w']) ? $this->cssConverter->ConvertToMM($params['w'], $this->coordinates['w']) : 0.;
        $h = isset($params['h']) ? $this->cssConverter->ConvertToMM($params['h'], $this->coordinates['h']) : 0.;

        $this->pdf->svgRect($x, $y, $w, $h, $style);
    }

    /**
     * @param $params
     * @param $styles
     */
    public function polygon($params, $styles)
    {
        $this->polyline($params, $styles, true);
    }

    /**
     * @param array $params
     * @param array $styles
     * @param bool  $closed
     */
    public function polyline($params, $styles, $closed = false)
    {
        $style = $this->pdf->svgSetStyle($styles);

        $path = isset($params['points']) ? $params['points'] : null;
        if ($path) {
            $path = str_replace(',', ' ', $path);
            $path = preg_replace('/[\s]+/', ' ', trim($path));

            // prepare the path
            $path = explode(' ', $path);
            foreach ($path as $k => $v) {
                $path[$k] = trim($v);
                if ($path[$k]==='') {
                    unset($path[$k]);
                }
            }
            $path = array_values($path);

            $actions = array();
            for ($k=0; $k<count($path); $k+=2) {
                $actions[] = array(
                    ($k ? 'L' : 'M') ,
                    $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['w']),
                    $this->cssConverter->ConvertToMM($path[$k+1], $this->coordinates['h'])
                );
            }
            if ($closed) {
                $actions[] = array('z');
            }

            // drawing
            $this->pdf->svgPolygone($actions, $style);
        }
    }

    /**
     * @param $params
     * @param $styles
     *
     * @throws Exception\HtmlParsingException
     */
    public function path($params, $styles)
    {
        $style = $this->pdf->svgSetStyle($styles);

        $path = isset($params['d']) ? $params['d'] : null;

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
                if ($path[$k]==='') {
                    unset($path[$k]);
                }
            }
            $path = array_values($path);

            // read each actions in the path
            $actions = array();
            $lastAction = null; // last action found
            for ($k=0; $k<count($path); true) {

                // for this actions, we can not have multi coordinate
                if (in_array($lastAction, array('z', 'Z'))) {
                    $lastAction = null;
                }

                // read the new action (forcing if no action before)
                if (preg_match('/^[a-z]+$/i', $path[$k]) || $lastAction===null) {
                    $lastAction = $path[$k];
                    $k++;
                }

                // current action
                $action = array();
                $action[] = $lastAction;
                switch ($lastAction) {
                    case 'C':
                    case 'c':
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['w']);    // x1
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+1], $this->coordinates['h']);    // y1
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+2], $this->coordinates['w']);    // x2
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+3], $this->coordinates['h']);    // y2
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+4], $this->coordinates['w']);    // x
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+5], $this->coordinates['h']);    // y
                        $k+= 6;
                        break;

                    case 'Q':
                    case 'S':
                    case 'q':
                    case 's':
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['w']);    // x2
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+1], $this->coordinates['h']);    // y2
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+2], $this->coordinates['w']);    // x
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+3], $this->coordinates['h']);    // y
                        $k+= 4;
                        break;

                    case 'A':
                    case 'a':
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['w']);    // rx
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+1], $this->coordinates['h']);    // ry
                        $action[] = 1.*$path[$k+2];                                                        // angle de deviation de l'axe X
                        $action[] = ($path[$k+3]=='1') ? 1 : 0;                                            // large-arc-flag
                        $action[] = ($path[$k+4]=='1') ? 1 : 0;                                            // sweep-flag
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+5], $this->coordinates['w']);    // x
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+6], $this->coordinates['h']);    // y
                        $k+= 7;
                        break;

                    case 'M':
                    case 'L':
                    case 'T':
                    case 'm':
                    case 'l':
                    case 't':
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['w']);    // x
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+1], $this->coordinates['h']);    // y
                        $k+= 2;
                        break;

                    case 'H':
                    case 'h':
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['w']);    // x
                        $k+= 1;
                        break;

                    case 'V':
                    case 'v':
                        $action[] = $this->cssConverter->ConvertToMM($path[$k+0], $this->coordinates['h']);    // y
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

    /**
     * prepare a transform matrix for drawing a SVG graphic
     *
     * @param string $transform
     *
     * @return array $matrix
     */
    public function prepareTransform($transform)
    {
        // it can not be  empty
        if (!$transform) {
            return null;
        }

        // sctions must be like scale(...)
        if (!preg_match_all('/([a-z]+)\(([^\)]*)\)/isU', $transform, $match)) {
            return null;
        }

        // prepare the list of the actions
        $actions = array();

        // for actions
        for ($k=0; $k<count($match[0]); $k++) {
            // get the name of the action
            $name = strtolower($match[1][$k]);

            // get the parameters of the action
            $val = explode(',', trim($match[2][$k]));
            foreach ($val as $i => $j) {
                $val[$i] = trim($j);
            }

            // prepare the matrix, depending on the action
            switch ($name) {
                case 'scale':
                    if (!isset($val[0])) {
                        $val[0] = 1.;
                    } else {
                        $val[0] = 1.*$val[0];
                    }
                    if (!isset($val[1])) {
                        $val[1] = $val[0];
                    } else {
                        $val[1] = 1.*$val[1];
                    }
                    $actions[] = array($val[0],0,0,$val[1],0,0);
                    break;

                case 'translate':
                    if (!isset($val[0])) {
                        $val[0] = 0.;
                    } else {
                        $val[0] = $this->cssConverter->ConvertToMM($val[0], $this->coordinates['w']);
                    }
                    if (!isset($val[1])) {
                        $val[1] = 0.;
                    } else {
                        $val[1] = $this->cssConverter->ConvertToMM($val[1], $this->coordinates['h']);
                    }
                    $actions[] = array(1,0,0,1,$val[0],$val[1]);
                    break;

                case 'rotate':
                    if (!isset($val[0])) {
                        $val[0] = 0.;
                    } else {
                        $val[0] = $val[0]*M_PI/180.;
                    }
                    if (!isset($val[1])) {
                        $val[1] = 0.;
                    } else {
                        $val[1] = $this->cssConverter->ConvertToMM($val[1], $this->coordinates['w']);
                    }
                    if (!isset($val[2])) {
                        $val[2] = 0.;
                    } else {
                        $val[2] = $this->cssConverter->ConvertToMM($val[2], $this->coordinates['h']);
                    }
                    if ($val[1] || $val[2]) {
                        $actions[] = array(1,0,0,1,-$val[1],-$val[2]);
                    }
                    $actions[] = array(cos($val[0]),sin($val[0]),-sin($val[0]),cos($val[0]),0,0);
                    if ($val[1] || $val[2]) {
                        $actions[] = array(1,0,0,1,$val[1],$val[2]);
                    }
                    break;

                case 'skewx':
                    if (!isset($val[0])) {
                        $val[0] = 0.;
                    } else {
                        $val[0] = $val[0]*M_PI/180.;
                    }
                    $actions[] = array(1,0,tan($val[0]),1,0,0);
                    break;

                case 'skewy':
                    if (!isset($val[0])) {
                        $val[0] = 0.;
                    } else {
                        $val[0] = $val[0]*M_PI/180.;
                    }
                    $actions[] = array(1,tan($val[0]),0,1,0,0);
                    break;
                case 'matrix':
                    if (!isset($val[0])) {
                        $val[0] = 0.;
                    } else {
                        $val[0] = $val[0]*1.;
                    }
                    if (!isset($val[1])) {
                        $val[1] = 0.;
                    } else {
                        $val[1] = $val[1]*1.;
                    }
                    if (!isset($val[2])) {
                        $val[2] = 0.;
                    } else {
                        $val[2] = $val[2]*1.;
                    }
                    if (!isset($val[3])) {
                        $val[3] = 0.;
                    } else {
                        $val[3] = $val[3]*1.;
                    }
                    if (!isset($val[4])) {
                        $val[4] = 0.;
                    } else {
                        $val[4] = $this->cssConverter->ConvertToMM($val[4], $this->coordinates['w']);
                    }
                    if (!isset($val[5])) {
                        $val[5] = 0.;
                    } else {
                        $val[5] = $this->cssConverter->ConvertToMM($val[5], $this->coordinates['h']);
                    }
                    $actions[] =$val;
                    break;
            }
        }

        // if there are no actions => return
        if (!$actions) {
            return null;
        }

        // get the first matrix
        $m = $actions[0];
        unset($actions[0]);

        // foreach matrix => multiply to the last matrix
        foreach ($actions as $n) {
            $m = array(
                $m[0]*$n[0]+$m[2]*$n[1],
                $m[1]*$n[0]+$m[3]*$n[1],
                $m[0]*$n[2]+$m[2]*$n[3],
                $m[1]*$n[2]+$m[3]*$n[3],
                $m[0]*$n[4]+$m[2]*$n[5]+$m[4],
                $m[1]*$n[4]+$m[3]*$n[5]+$m[5]
            );
        }

        // return the matrix
        return $m;
    }
}
