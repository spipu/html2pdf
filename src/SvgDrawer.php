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
