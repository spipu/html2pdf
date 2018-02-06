<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
namespace Spipu\Html2Pdf;
use Spipu\Html2Pdf\Exception\HtmlParsingException;

/**
 * Class SvgDrawer
 */
class SvgDrawer
{
    /**
     * @var MyPdf
     */
    private $pdf;

    /**
     * @var array
     */
    private $properties;
    /**
     * @var CssConverter
     */
    private $cssConverter;

    /**
     * SvgDrawer constructor.
     *
     * @param MyPdf $pdf
     * @param CssConverter $cssConverter
     */
    public function __construct(
        MyPdf $pdf,
        CssConverter $cssConverter
    ) {

        $this->pdf = $pdf;
        $this->cssConverter = $cssConverter;
    }

    /**
     * Start Drawing
     *
     * @param array $properties
     * @throws HtmlParsingException
     */
    public function startDrawing($properties)
    {
        if ($this->isDrawing()) {
            $e = new HtmlParsingException('We are already in a draw tag');
            $e->setInvalidTag('draw');
            throw $e;
        }

        $this->properties = $properties;

        // init the translate matrix : (0,0) => (x, y)
        $this->pdf->doTransform(array(1,0,0,1,$this->properties['x'],$this->properties['y']));
        $this->pdf->setAlpha(1.);
    }

    /**
     * Stop Drawing
     */
    public function stopDrawing()
    {
        $this->properties = null;

        $this->pdf->setAlpha(1.);
        $this->pdf->undoTransform();
        $this->pdf->clippingPathStop();
    }

    /**
     * Are we drawing ?
     *
     * @return bool
     */
    public function isDrawing()
    {
        return is_array($this->properties);
    }

    /**
     * Get the property
     *
     * @param string $key
     * @return mixed
     */
    public function getProperty($key)
    {
        return $this->properties[$key];
    }


    /**
     * prepare a transform matrix
     *
     * @param  string $transform
     * @return array
     */
    public function prepareTransform($transform)
    {
        // it can not be  empty
        if (!$transform) {
            return null;
        }

        // sections must be like scale(...)
        if (!preg_match_all('/([a-z]+)\(([^\)]*)\)/isU', $transform, $match)) {
            return null;
        }

        // prepare the list of the actions
        $actions = array();

        // for actions
        $amountMatches = count($match[0]);
        for ($k=0; $k < $amountMatches; $k++) {
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
                        $val[0] = $this->cssConverter->convertToMM($val[0], $this->properties['w']);
                    }
                    if (!isset($val[1])) {
                        $val[1] = 0.;

                    } else {
                        $val[1] = $this->cssConverter->convertToMM($val[1], $this->properties['h']);
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
                        $val[1] = $this->cssConverter->convertToMM($val[1], $this->properties['w']);
                    }
                    if (!isset($val[2])) {
                        $val[2] = 0.;

                    } else {
                        $val[2] = $this->cssConverter->convertToMM($val[2], $this->properties['h']);
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
                        $val[4] = $this->cssConverter->convertToMM($val[4], $this->properties['w']);
                    }
                    if (!isset($val[5])) {
                        $val[5] = 0.;

                    } else {
                        $val[5] = $this->cssConverter->convertToMM($val[5], $this->properties['h']);
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
