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

/**
 * Class CssConverter
 */
class CssConverter
{
    private $htmlColor   = array(); // list of the HTML colors

    /**
     * fontsize ratios
     * @var float[]
     */
    private $fontSizeRatio = [
        'smaller'  => 0.8,
        'larger'   => 1.25,
        'xx-small' => 0.512,
        'x-small'  => 0.64,
        'small'    => 0.8,
        'medium'   => 1.,
        'large'    => 1.25,
        'x-large'  => 1.5625,
        'xx-large' => 1.953125,
    ];

    public function __construct()
    {
        $this->htmlColor = \TCPDF_COLORS::$webcolor;
    }

    /**
     * convert a distance to mm
     *
     * @param string $css distance to convert
     * @param float  $old parent distance
     *
     * @return float
     */
    public function convertToMM($css, $old = 0.)
    {
        $css = trim($css);
        if (preg_match('/^[0-9\.\-]+$/isU', $css)) {
            $css.= 'px';
        }
        if (preg_match('/^[0-9\.\-]+px$/isU', $css)) {
            $css = 25.4/96. * str_replace('px', '', $css);
        } elseif (preg_match('/^[0-9\.\-]+pt$/isU', $css)) {
            $css = 25.4/72. * str_replace('pt', '', $css);
        } elseif (preg_match('/^[0-9\.\-]+in$/isU', $css)) {
            $css = 25.4 * str_replace('in', '', $css);
        } elseif (preg_match('/^[0-9\.\-]+mm$/isU', $css)) {
            $css = 1.*str_replace('mm', '', $css);
        } elseif (preg_match('/^[0-9\.\-]+%$/isU', $css)) {
            $css = 1.*$old*str_replace('%', '', $css)/100.;
        } else {
            $css = null;
        }

        return $css;
    }

    /**
     * @param string $css    font size to convert
     * @param float  $parent parent font size
     * @return float
     */
    public function convertFontSize($css, $parent = 0.)
    {
        $css = trim($css);
        if (array_key_exists($css, $this->fontSizeRatio)) {
            $css = ($this->fontSizeRatio[$css] * $parent).'mm';
        }

        return $this->convertToMM($css, $parent);
    }

    /**
     * convert a css radius
     *
     * @access public
     * @param  string $css
     * @return array
     */
    public function convertToRadius($css)
    {
        // explode the value
        $css = explode(' ', $css);

        foreach ($css as $k => $v) {
            $v = trim($v);
            if ($v !== '') {
                $v = $this->convertToMM($v, 0);
                if ($v !== null) {
                    $css[$k] = $v;
                } else {
                    unset($css[$k]);
                }
            } else {
                unset($css[$k]);
            }
        }

        return array_values($css);
    }

    /**
     * convert a css color to an RGB array
     *
     * @param string   $css
     * @param &boolean $res
     *
     * @return array (r, g, b)
     */
    public function convertToColor($css, &$res)
    {
        // prepare the value
        $css = trim($css);
        $res = true;

        // if transparent => return null
        if (strtolower($css) === 'transparent') {
            return array(null, null, null);
        }

        // HTML color
        if (isset($this->htmlColor[strtolower($css)])) {
            $css = $this->htmlColor[strtolower($css)];
            $r = floatVal(hexdec(substr($css, 0, 2)));
            $g = floatVal(hexdec(substr($css, 2, 2)));
            $b = floatVal(hexdec(substr($css, 4, 2)));
            return array($r, $g, $b);
        }

        // like #FFFFFF
        if (preg_match('/^#[0-9A-Fa-f]{6}$/isU', $css)) {
            $r = floatVal(hexdec(substr($css, 1, 2)));
            $g = floatVal(hexdec(substr($css, 3, 2)));
            $b = floatVal(hexdec(substr($css, 5, 2)));
            return array($r, $g, $b);
        }

        // like #FFF
        if (preg_match('/^#[0-9A-F]{3}$/isU', $css)) {
            $r = floatVal(hexdec(substr($css, 1, 1).substr($css, 1, 1)));
            $g = floatVal(hexdec(substr($css, 2, 1).substr($css, 2, 1)));
            $b = floatVal(hexdec(substr($css, 3, 1).substr($css, 3, 1)));
            return array($r, $g, $b);
        }

        // like rgb(100, 100, 100)
        $sub = '[\s]*([0-9%\.]+)[\s]*';
        if (preg_match('/rgb\('.$sub.','.$sub.','.$sub.'\)/isU', $css, $match)) {
            $r = $this->convertSubColor($match[1]);
            $g = $this->convertSubColor($match[2]);
            $b = $this->convertSubColor($match[3]);
            return array($r * 255., $g * 255., $b * 255.);
        }

        // like cmyk(100, 100, 100, 100)
        $sub = '[\s]*([0-9%\.]+)[\s]*';
        if (preg_match('/cmyk\('.$sub.','.$sub.','.$sub.','.$sub.'\)/isU', $css, $match)) {
            $c = $this->convertSubColor($match[1]);
            $m = $this->convertSubColor($match[2]);
            $y = $this->convertSubColor($match[3]);
            $k = $this->convertSubColor($match[4]);
            return array($c * 100., $m * 100., $y * 100., $k * 100.);
        }

        $res = false;
        return array(0., 0., 0.);
    }

    /**
     * color value to convert
     *
     * @access protected
     * @param  string $c
     * @return float $c 0.->1.
     */
    protected function convertSubColor($c)
    {
        if (substr($c, -1) === '%') {
            $c = floatVal(substr($c, 0, -1)) / 100.;
        } else {
            $c = floatVal($c);
            if ($c > 1) {
                $c = $c / 255.;
            }
        }

        return $c;
    }


    /**
     * Analyse a background
     *
     * @param  string $css css background properties
     * @param  &array $value parsed values (by reference, because, there is a legacy of the parent CSS properties)
     *
     * @return void
     */
    public function convertBackground($css, &$value)
    {
        // is there an image ?
        $text = '/url\(([^)]*)\)/isU';
        if (preg_match($text, $css, $match)) {
            // get the image
            $value['image'] = $this->convertBackgroundImage($match[0]);

            // remove if from the css properties
            $css = preg_replace($text, '', $css);
            $css = preg_replace('/[\s]+/', ' ', $css);
        }

        // protect some spaces
        $css = preg_replace('/,[\s]+/', ',', $css);

        // explode the values
        $css = explode(' ', $css);

        // background position to parse
        $pos = '';

        // foreach value
        foreach ($css as $val) {
            // try to parse the value as a color
            $ok = false;
            $color = $this->convertToColor($val, $ok);

            // if ok => it is a color
            if ($ok) {
                $value['color'] = $color;
                // else if transparent => no color
            } elseif ($val === 'transparent') {
                $value['color'] = null;
                // else
            } else {
                // try to parse the value as a repeat
                $repeat = $this->convertBackgroundRepeat($val);

                // if ok => it is repeated
                if ($repeat) {
                    $value['repeat'] = $repeat;
                    // else => it could only be a position
                } else {
                    $pos.= ($pos ? ' ' : '').$val;
                }
            }
        }

        // if we have a position to parse
        if ($pos) {
            // try to read it
            $pos = $this->convertBackgroundPosition($pos, $ok);
            if ($ok) {
                $value['position'] = $pos;
            }
        }
    }

    /**
     * Parse a background color
     *
     * @param  string $css
     *
     * @return float[]|null $value
     */
    public function convertBackgroundColor($css)
    {
        $res = null;
        if ($css === 'transparent') {
            return null;
        }

        return $this->convertToColor($css, $res);
    }

    /**
     * Parse a background image
     *
     * @param  string $css
     *
     * @return string|null $value
     */
    public function convertBackgroundImage($css)
    {
        if ($css === 'none') {
            return null;
        }

        if (preg_match('/^url\(([^)]*)\)$/isU', $css, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Parse a background position
     *
     * @param  string $css
     * @param  boolean &$res flag if convert is ok or not
     *
     * @return array (x, y)
     */
    public function convertBackgroundPosition($css, &$res)
    {
        // init the res
        $res = false;

        // explode the value
        $css = explode(' ', $css);

        // we must have 2 values. if 0 or >2 : error. if 1 => put center for 2
        if (count($css)<2) {
            if (!$css[0]) {
                return null;
            }
            $css[1] = 'center';
        }
        if (count($css)>2) {
            return null;
        }

        // prepare the values
        $x = 0;
        $y = 0;
        $res = true;

        // convert the first value
        if ($css[0] === 'left') {
            $x = '0%';
        } elseif ($css[0] === 'center') {
            $x = '50%';
        } elseif ($css[0] === 'right') {
            $x = '100%';
        } elseif ($css[0] === 'top') {
            $y = '0%';
        } elseif ($css[0] === 'bottom') {
            $y = '100%';
        } elseif (preg_match('/^[-]?[0-9\.]+%$/isU', $css[0])) {
            $x = $css[0];
        } elseif ($this->convertToMM($css[0])) {
            $x = $this->convertToMM($css[0]);
        } else {
            $res = false;
        }

        // convert the second value
        if ($css[1] === 'left') {
            $x = '0%';
        } elseif ($css[1] === 'right') {
            $x = '100%';
        } elseif ($css[1] === 'top') {
            $y = '0%';
        } elseif ($css[1] === 'center') {
            $y = '50%';
        } elseif ($css[1] === 'bottom') {
            $y = '100%';
        } elseif (preg_match('/^[-]?[0-9\.]+%$/isU', $css[1])) {
            $y = $css[1];
        } elseif ($this->convertToMM($css[1])) {
            $y = $this->convertToMM($css[1]);
        } else {
            $res = false;
        }

        // return the values
        return array($x, $y);
    }

    /**
     * Parse a background repeat
     *
     * @param  string $css
     *
     * @return array|null background repeat as array
     */
    public function convertBackgroundRepeat($css)
    {
        switch ($css) {
            case 'repeat':
                return array(true, true);
            case 'repeat-x':
                return array(true, false);
            case 'repeat-y':
                return array(false, true);
            case 'no-repeat':
                return array(false, false);
        }
        return null;
    }
}
