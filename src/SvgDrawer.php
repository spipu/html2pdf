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
        $this->pdf->doTransform(array(1,0,0,1,$this->getProperty('x'),$this->getProperty('y')));
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
            $values = [];
            $string = trim($match[2][$k]);
            if ($string !== '') {
                $values = explode(',', $string);
            }
            foreach ($values as $key => $value) {
                $value = trim($value);
                if ($value === '') {
                    unset($values[$key]);
                    continue;
                }

                $values[$key] = $value;
            }

            // prepare the matrix, depending on the action
            switch ($name) {
                case 'scale':
                    if (!array_key_exists(0, $values)) {
                        $values[0] = 1.;
                    }

                    if (!array_key_exists(1, $values)) {
                        $values[1] = $values[0];
                    }

                    $values[0] = floatval($values[0]);
                    $values[1] = floatval($values[1]);

                    $actions[] = array($values[0],0.,0.,$values[1],0.,0.);
                    break;

                case 'translate':
                    if (!array_key_exists(0, $values)) {
                        $values[0] = 0.;
                    }

                    if (!array_key_exists(1, $values)) {
                        $values[1] = 0.;
                    }

                    $values[0] = $this->cssConverter->convertToMM($values[0], $this->getProperty('w'));
                    $values[1] = $this->cssConverter->convertToMM($values[1], $this->getProperty('h'));

                    $actions[] = array(1.,0.,0.,1.,$values[0],$values[1]);
                    break;

                case 'rotate':
                    if (!array_key_exists(0, $values)) {
                        $values[0] = 0.;
                    }
                    if (!array_key_exists(1, $values)) {
                        $values[1] = 0.;
                    }
                    if (!array_key_exists(2, $values)) {
                        $values[2] = 0.;
                    }

                    $values[0] = $values[0]*M_PI/180.;
                    $values[1] = $this->cssConverter->convertToMM($values[1], $this->getProperty('w'));
                    $values[2] = $this->cssConverter->convertToMM($values[2], $this->getProperty('h'));

                    if ($values[1] || $values[2]) {
                        $actions[] = array(1.,0.,0.,1.,-$values[1],-$values[2]);
                    }

                    $actions[] = array(cos($values[0]),sin($values[0]),-sin($values[0]),cos($values[0]),0.,0.);

                    if ($values[1] || $values[2]) {
                        $actions[] = array(1.,0.,0.,1.,$values[1],$values[2]);
                    }
                    break;

                case 'skewx':
                    if (!array_key_exists(0, $values)) {
                        $values[0] = 0.;
                    }

                    $values[0] = $values[0]*M_PI/180.;

                    $actions[] = array(1.,0.,tan($values[0]),1.,0.,0.);
                    break;

                case 'skewy':
                    if (!array_key_exists(0, $values)) {
                        $values[0] = 0.;
                    }

                    $values[0] = $values[0]*M_PI/180.;

                    $actions[] = array(1.,tan($values[0]),0.,1.,0.,0.);
                    break;

                case 'matrix':
                    if (!array_key_exists(0, $values)) {
                        $values[0] = 0.;
                    }

                    if (!array_key_exists(1, $values)) {
                        $values[1] = 0.;
                    }

                    if (!array_key_exists(2, $values)) {
                        $values[2] = 0.;
                    }

                    if (!array_key_exists(3, $values)) {
                        $values[3] = 0.;
                    }

                    if (!array_key_exists(4, $values)) {
                        $values[4] = 0.;
                    }

                    if (!array_key_exists(5, $values)) {
                        $values[5] = 0.;
                    }

                    $values[0] = floatval($values[0]);
                    $values[1] = floatval($values[1]);
                    $values[2] = floatval($values[2]);
                    $values[3] = floatval($values[3]);
                    $values[4] = $this->cssConverter->convertToMM($values[4], $this->getProperty('w'));
                    $values[5] = $this->cssConverter->convertToMM($values[5], $this->getProperty('h'));

                    $actions[] = $values;
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
