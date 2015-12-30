<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf;

/**
 * Class CssConverter
 */
class CssConverter
{
    private $htmlColor   = array(); // list of the HTML colors

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
     * convert a css radius
     *
     * @access public
     * @param  string $css
     * @return float  $value
     */
    public function convertToRadius($css)
    {
        // explode the value
        $css = explode(' ', $css);

        foreach ($css as $k => $v) {
            $v = trim($v);
            if ($v) {
                $v = $this->convertToMM($v, 0);
                if ($v!==null) {
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
        if (strtolower($css) == 'transparent') {
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
        if (substr($c, -1) == '%') {
            $c = floatVal(substr($c, 0, -1)) / 100.;
        } else {
            $c = floatVal($c);
            if ($c > 1) {
                $c = $c / 255.;
            }
        }

        return $c;
    }
}
