<?php
/**
 * Html2Pdf Library - parsing Css class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Parsing;

use Spipu\Html2Pdf\CssConverter;
use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\MyPdf;

class Css
{
    /**
     * @var TagParser
     */
    protected $tagParser;

    /**
     * @var CssConverter
     */
    protected $cssConverter;

    /**
     * Reference to the pdf object
     *
     * @var MyPdf
     */
    protected $pdf         = null;

    protected $onlyLeft    = false; // flag if we are in a sub html => only "text-align:left" is used
    protected $defaultFont = null;  // default font to use if the asked font does not exist

    public $value        = array(); // current values
    public $css          = array(); // css values
    public $cssKeys      = array(); // css key, for the execution order
    public $table        = array(); // level history

    protected $authorizedSchemes = ['file', 'http', 'https'];

    /**
     * Constructor
     *
     * @param MyPdf        $pdf reference to the PDF $object
     * @param TagParser    $tagParser
     * @param CssConverter $cssConverter
     */
    public function __construct(&$pdf, TagParser $tagParser, CssConverter $cssConverter)
    {
        $this->cssConverter = $cssConverter;
        $this->init();
        $this->setPdfParent($pdf);
        $this->tagParser = $tagParser;
    }

    /**
     * Set the $pdf parent object
     *
     * @param  MyPdf &$pdf reference to the Html2Pdf parent
     *
     * @return void
     */
    public function setPdfParent(&$pdf)
    {
        $this->pdf = &$pdf;
    }

    /**
     * Inform that we want only "test-align:left" because we are in a sub HTML
     *
     * @return void
     */
    public function setOnlyLeft()
    {
        $this->value['text-align'] = 'left';
        $this->onlyLeft = true;
    }

    /**
     * Get the vales of the parent, if exist
     *
     * @return array CSS values
     */
    public function getOldValues()
    {
        return isset($this->table[count($this->table)-1]) ? $this->table[count($this->table)-1] : $this->value;
    }

   /**
    * Define the Default Font to use, if the font does not exist, or if no font asked
    *
    * @param string  default font-family. If null : Arial for no font asked, and error fot ont does not exist
    *
    * @return string  old default font-family
    */
    public function setDefaultFont($default = null)
    {
        $old = $this->defaultFont;
        $this->defaultFont = $default;
        if ($default) {
            $this->value['font-family'] = $default;
        }
        return $old;
    }

    /**
     * Init the object
     *
     * @return void
     */
    protected function init()
    {
        // init the Style
        $this->table = array();
        $this->value = array();
        $this->initStyle();

        // Init the styles without legacy
        $this->resetStyle();
    }

    /**
     * Init the CSS Style
     *
     * @return void
     */
    public function initStyle()
    {
        $this->value['id_tag']           = 'body';       // tag name
        $this->value['id_name']          = null;         // tag - attribute name
        $this->value['id_id']            = null;         // tag - attribute id
        $this->value['id_class']         = null;         // tag - attribute class
        $this->value['id_lst']           = array('*');   // tag - list of legacy
        $this->value['mini-size']        = 1.;           // specific size report for sup, sub
        $this->value['mini-decal']       = 0;            // specific position report for sup, sub
        $this->value['font-family']      = defined('PDF_FONT_NAME_MAIN') ? PDF_FONT_NAME_MAIN : 'Arial';
        $this->value['font-bold']        = false;
        $this->value['font-italic']      = false;
        $this->value['font-underline']   = false;
        $this->value['font-overline']    = false;
        $this->value['font-linethrough'] = false;
        $this->value['text-transform']   = 'none';
        $this->value['font-size']        = $this->cssConverter->convertFontSize('10pt');
        $this->value['text-indent']      = 0;
        $this->value['text-align']       = 'left';
        $this->value['vertical-align']   = 'middle';
        $this->value['line-height']      = 'normal';

        $this->value['position']         = null;
        $this->value['x']                = null;
        $this->value['y']                = null;
        $this->value['width']            = 0;
        $this->value['height']           = 0;
        $this->value['top']              = null;
        $this->value['right']            = null;
        $this->value['bottom']           = null;
        $this->value['left']             = null;
        $this->value['float']            = null;
        $this->value['display']          = null;
        $this->value['rotate']           = null;
        $this->value['overflow']         = 'visible';

        $this->value['color']            = array(0, 0, 0);
        $this->value['background']       = array(
            'color'    => null,
            'image'    => null,
            'position' => null,
            'repeat'   => null
        );
        $this->value['border']           = array();
        $this->value['padding']          = array();
        $this->value['margin']           = array();
        $this->value['margin-auto']      = false;

        $this->value['list-style-type']  = '';
        $this->value['list-style-image'] = '';

        $this->value['xc'] = null;
        $this->value['yc'] = null;

        $this->value['page-break-before'] = null;
        $this->value['page-break-after']  = null;
    }

    /**
     * Init the CSS Style without legacy
     *
     * @param string tag name
     *
     * @return void
     */
    public function resetStyle($tagName = '')
    {
        // prepare some values
        $border = $this->readBorder('solid 1px #000000');
        $units = array(
            '1px' => $this->cssConverter->convertToMM('1px'),
            '5px' => $this->cssConverter->convertToMM('5px'),
        );

        // prepare the Collapse attribute
        $collapse = isset($this->value['border']['collapse']) ? $this->value['border']['collapse'] : false;
        if (!in_array($tagName, array('tr', 'td', 'th', 'thead', 'tbody', 'tfoot'))) {
            $collapse = false;
        }

        // set the global css values
        $this->value['position']   = null;
        $this->value['x']          = null;
        $this->value['y']          = null;
        $this->value['width']      = 0;
        $this->value['height']     = 0;
        $this->value['top']        = null;
        $this->value['right']      = null;
        $this->value['bottom']     = null;
        $this->value['left']       = null;
        $this->value['float']      = null;
        $this->value['display']    = null;
        $this->value['rotate']     = null;
        $this->value['overflow']   = 'visible';
        $this->value['background'] = array('color' => null, 'image' => null, 'position' => null, 'repeat' => null);
        $this->value['border']     = array(
            't' => $this->readBorder('none'),
            'r' => $this->readBorder('none'),
            'b' => $this->readBorder('none'),
            'l' => $this->readBorder('none'),
            'radius' => array(
                'tl' => array(0, 0),
                'tr' => array(0, 0),
                'br' => array(0, 0),
                'bl' => array(0, 0)
            ),
            'collapse' => $collapse,
        );

        // specific values for some tags
        if (!in_array($tagName, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
            $this->value['margin'] = array('t'=>0,'r'=>0,'b'=>0,'l'=>0);
        }

        if (in_array($tagName, array('input', 'select', 'textarea'))) {
            $this->value['border']['t'] = null;
            $this->value['border']['r'] = null;
            $this->value['border']['b'] = null;
            $this->value['border']['l'] = null;
        }

        if ($tagName === 'p') {
            $this->value['margin']['t'] = null;
            $this->value['margin']['b'] = null;
        }
        if ($tagName === 'blockquote') {
            $this->value['margin']['t'] = 3;
            $this->value['margin']['r'] = 3;
            $this->value['margin']['b'] = 3;
            $this->value['margin']['l'] = 6;
        }
        $this->value['margin-auto'] = false;

        if (in_array($tagName, array('blockquote', 'div', 'fieldset'))) {
            $this->value['vertical-align'] = 'top';
        }

        if (in_array($tagName, array('fieldset', 'legend'))) {
            $this->value['border'] = array(
                't' => $border,
                'r' => $border,
                'b' => $border,
                'l' => $border,
                'radius' => array(
                    'tl' => array($units['5px'], $units['5px']),
                    'tr' => array($units['5px'], $units['5px']),
                    'br' => array($units['5px'], $units['5px']),
                    'bl' => array($units['5px'], $units['5px'])
                ),
                'collapse' => false,
            );
        }

        if (in_array($tagName, array('ul', 'li'))) {
            $this->value['list-style-type']  = '';
            $this->value['list-style-image'] = '';
        }

        if (!in_array($tagName, array('tr', 'td'))) {
            $this->value['padding'] = array(
                't' => 0,
                'r' => 0,
                'b' => 0,
                'l' => 0
            );
        } else {
            $this->value['padding'] = array(
                't' => $units['1px'],
                'r' => $units['1px'],
                'b' => $units['1px'],
                'l' => $units['1px']
            );
        }

        if ($tagName === 'hr') {
            $this->value['border'] = array(
                't' => $border,
                'r' => $border,
                'b' => $border,
                'l' => $border,
                'radius' => array(
                    'tl' => array(0, 0),
                    'tr' => array(0, 0),
                    'br' => array(0, 0),
                    'bl' => array(0, 0)
                ),
                'collapse' => false,
            );
            $this->cssConverter->convertBackground('#FFFFFF', $this->value['background']);
        }

        $this->value['xc'] = null;
        $this->value['yc'] = null;
    }

    /**
     * Init the PDF Font
     *
     * @return void
     */
    public function fontSet()
    {
        $family = strtolower($this->value['font-family']);

        $b = ($this->value['font-bold']        ? 'B' : '');
        $i = ($this->value['font-italic']      ? 'I' : '');
        $u = ($this->value['font-underline']   ? 'U' : '');
        $d = ($this->value['font-linethrough'] ? 'D' : '');
        $o = ($this->value['font-overline']    ? 'O' : '');

        // font style
        $style = $b.$i;

        if ($this->defaultFont) {
            if ($family === 'arial') {
                $family='helvetica';
            } elseif ($family === 'symbol' || $family === 'zapfdingbats') {
                $style='';
            }

            $fontkey = $family.$style;
            if (!$this->pdf->isLoadedFont($fontkey)) {
                $family = $this->defaultFont;
            }
        }

        if ($family === 'arial') {
            $family='helvetica';
        } elseif ($family === 'symbol' || $family === 'zapfdingbats') {
            $style='';
        }

        // complete style
        $style.= $u.$d.$o;

        // size : mm => pt
        $size = $this->value['font-size'];
        $size = 72 * $size / 25.4;

        // apply the font
        $this->pdf->SetFont($family, $style, $this->value['mini-size']*$size);
        $this->pdf->SetTextColorArray($this->value['color']);
        if ($this->value['background']['color']) {
            $this->pdf->SetFillColorArray($this->value['background']['color']);
        } else {
            $this->pdf->SetFillColor(255);
        }
    }

    /**
     * Add a level in the CSS history
     *
     * @return void
     */
    public function save()
    {
        array_push($this->table, $this->value);
    }

    /**
     * Remove a level in the CSS history
     *
     * @return void
     */
    public function load()
    {
        if (count($this->table)) {
            $this->value = array_pop($this->table);
        }
    }

    /**
     * Restore the Y position (used after a span)
     *
     * @return void
     */
    public function restorePosition()
    {
        if ($this->value['y'] == $this->pdf->GetY()) {
            $this->pdf->SetY($this->value['yc'], false);
        }
    }

    /**
     * Set the New position for the current Tag
     *
     * @return void
     */
    public function setPosition()
    {
        // get the current position
        $currentX = $this->pdf->GetX();
        $currentY = $this->pdf->GetY();

        // save it
        $this->value['xc'] = $currentX;
        $this->value['yc'] = $currentY;

        if ($this->value['position'] === 'relative' || $this->value['position'] === 'absolute') {
            if ($this->value['right'] !== null) {
                $x = $this->getLastWidth(true) - $this->value['right'] - $this->value['width'];
                if ($this->value['margin']['r']) {
                    $x-= $this->value['margin']['r'];
                }
            } else {
                $x = $this->value['left'];
                if ($this->value['margin']['l']) {
                    $x+= $this->value['margin']['l'];
                }
            }

            if ($this->value['bottom'] !== null) {
                $y = $this->getLastHeight(true) - $this->value['bottom'] - $this->value['height'];
                if ($this->value['margin']['b']) {
                    $y-= $this->value['margin']['b'];
                }
            } else {
                $y = $this->value['top'];
                if ($this->value['margin']['t']) {
                    $y+= $this->value['margin']['t'];
                }
            }

            if ($this->value['position'] === 'relative') {
                $this->value['x'] = $currentX + $x;
                $this->value['y'] = $currentY + $y;
            } else {
                $this->value['x'] = $this->getLastAbsoluteX()+$x;
                $this->value['y'] = $this->getLastAbsoluteY()+$y;
            }
        } else {
            $this->value['x'] = $currentX;
            $this->value['y'] = $currentY;
            if ($this->value['margin']['l']) {
                $this->value['x']+= $this->value['margin']['l'];
            }
            if ($this->value['margin']['t']) {
                $this->value['y']+= $this->value['margin']['t'];
            }
        }

        // save the new position
        $this->pdf->SetXY($this->value['x'], $this->value['y']);
    }

    /**
     * Analyse the CSS style to convert it into Form style
     *
     * @return array styles
     */
    public function getFormStyle()
    {
        $prop = array(
            'alignment' => $this->value['text-align']
        );

        if (isset($this->value['background']['color']) && is_array($this->value['background']['color'])) {
            $prop['fillColor'] = $this->value['background']['color'];
        }

        if (isset($this->value['border']['t']['color'])) {
            $prop['strokeColor'] = $this->value['border']['t']['color'];
        }

        if (isset($this->value['border']['t']['width'])) {
            $prop['lineWidth'] = $this->value['border']['t']['width'];
        }

        if (isset($this->value['border']['t']['type'])) {
            $prop['borderStyle'] = $this->value['border']['t']['type'];
        }

        if (!empty($this->value['color'])) {
            $prop['textColor'] = $this->value['color'];
        }

        if (!empty($this->value['font-size'])) {
            $prop['textSize'] = $this->value['font-size'];
        }

        return $prop;
    }

    /**
     * Analise the CSS style to convert it into SVG style
     *
     * @param string tag name
     * @param array  styles
     *
     * @return array svg style
     */
    public function getSvgStyle($tagName, &$param)
    {
        // prepare
        $tagName = strtolower($tagName);
        $id   = isset($param['id'])   ? strtolower(trim($param['id']))   : null;
        if (!$id) {
            $id   = null;
        }
        $name = isset($param['name']) ? strtolower(trim($param['name'])) : null;
        if (!$name) {
            $name = null;
        }

        // read the class attribute
        $class = array();
        $tmp = isset($param['class']) ? strtolower(trim($param['class'])) : '';
        $tmp = explode(' ', $tmp);
        foreach ($tmp as $v) {
            $v = trim($v);
            if ($v) {
                $class[] = $v;
            }
        }

        // identify the tag, and the direct styles
        $this->value['id_tag'] = $tagName;
        $this->value['id_name']   = $name;
        $this->value['id_id']     = $id;
        $this->value['id_class']  = $class;
        $this->value['id_lst']    = array();
        $this->value['id_lst'][] = '*';
        $this->value['id_lst'][] = $tagName;
        if (!isset($this->value['svg'])) {
            $this->value['svg'] = array(
                'stroke'         => null,
                'stroke-width'   => $this->cssConverter->convertToMM('1pt'),
                'fill'           => null,
                'fill-opacity'   => null,
            );
        }

        if (count($class)) {
            foreach ($class as $v) {
                $this->value['id_lst'][] = '*.'.$v;
                $this->value['id_lst'][] = '.'.$v;
                $this->value['id_lst'][] = $tagName.'.'.$v;
            }
        }
        if ($id) {
            $this->value['id_lst'][] = '*#'.$id;
            $this->value['id_lst'][] = '#'.$id;
            $this->value['id_lst'][] = $tagName.'#'.$id;
        }

        // CSS style
        $styles = $this->getFromCSS();

        // adding the style from the tag
        $styles = array_merge($styles, $param['style']);

        if (isset($styles['stroke'])) {
            $this->value['svg']['stroke']       = $this->cssConverter->convertToColor($styles['stroke'], $res);
        }
        if (isset($styles['stroke-width'])) {
            $this->value['svg']['stroke-width'] = $this->cssConverter->convertToMM($styles['stroke-width']);
        }
        if (isset($styles['fill'])) {
            $this->value['svg']['fill']         = $this->cssConverter->convertToColor($styles['fill'], $res);
        }
        if (isset($styles['fill-opacity'])) {
            $this->value['svg']['fill-opacity'] = 1.*$styles['fill-opacity'];
        }

        return $this->value['svg'];
    }

    /**
     * Analyse the CSS properties from the HTML parsing
     *
     * @param string $tagName
     * @param array  $param
     * @param array  $legacy
     *
     * @return boolean
     */
    public function analyse($tagName, &$param, $legacy = null)
    {
        // prepare the information
        $tagName = strtolower($tagName);
        $id   = isset($param['id'])   ? strtolower(trim($param['id']))    : null;
        if (!$id) {
            $id   = null;
        }
        $name = isset($param['name']) ? strtolower(trim($param['name']))  : null;
        if (!$name) {
            $name = null;
        }

        // get the class names to use
        $class = array();
        $tmp = isset($param['class']) ? strtolower(trim($param['class'])) : '';
        $tmp = explode(' ', $tmp);
        
        // replace some values
        $toReplace = array(
            '[[page_cu]]' => $this->pdf->getMyNumPage()
        );
        
        foreach ($tmp as $v) {
            $v = trim($v);
            if (strlen($v)>0) {
                $v = str_replace(array_keys($toReplace), array_values($toReplace), $v);
            }
            if ($v) {
                $class[] = $v;
            }
        }

        // prepare the values, and the list of css tags to identify
        $this->value['id_tag']   = $tagName;
        $this->value['id_name']  = $name;
        $this->value['id_id']    = $id;
        $this->value['id_class'] = $class;
        $this->value['id_lst']   = array();
        $this->value['id_lst'][] = '*';
        $this->value['id_lst'][] = $tagName;
        if (count($class)) {
            foreach ($class as $v) {
                $this->value['id_lst'][] = '*.'.$v;
                $this->value['id_lst'][] = '.'.$v;
                $this->value['id_lst'][] = $tagName.'.'.$v;
            }
        }
        if ($id) {
            $this->value['id_lst'][] = '*#'.$id;
            $this->value['id_lst'][] = '#'.$id;
            $this->value['id_lst'][] = $tagName.'#'.$id;
        }

        // get the css styles from class
        $styles = $this->getFromCSS();

        // merge with the css styles from tag
        $styles = array_merge($styles, $param['style']);
        if (isset($param['allwidth']) && !isset($styles['width'])) {
            $styles['width'] = '100%';
        }

        // reset some styles, depending on the tag name
        $this->resetStyle($tagName);

        // add the legacy values
        if ($legacy) {
            foreach ($legacy as $legacyName => $legacyValue) {
                if (is_array($legacyValue)) {
                    foreach ($legacyValue as $legacy2Name => $legacy2Value) {
                        $this->value[$legacyName][$legacy2Name] = $legacy2Value;
                    }
                } else {
                    $this->value[$legacyName] = $legacyValue;
                }
            }
        }

        // some flags
        $correctWidth = false;
        $noWidth = true;

        // read all the css styles
        foreach ($styles as $nom => $val) {
            switch ($nom) {
                case 'font-family':
                    $val = explode(',', $val);
                    $val = trim($val[0]);
                    $val = trim($val, '\'"');
                    if ($val && strtolower($val) !== 'inherit') {
                        $this->value['font-family'] = $val;
                    }
                    break;

                case 'font-weight':
                    $this->value['font-bold'] = ($val === 'bold');
                    break;

                case 'font-style':
                    $this->value['font-italic'] = ($val === 'italic');
                    break;

                case 'text-decoration':
                    $val = explode(' ', $val);
                    $this->value['font-underline']   = (in_array('underline', $val));
                    $this->value['font-overline']    = (in_array('overline', $val));
                    $this->value['font-linethrough'] = (in_array('line-through', $val));
                    break;

                case 'text-indent':
                    $this->value['text-indent'] = $this->cssConverter->convertToMM($val);
                    break;

                case 'text-transform':
                    if (!in_array($val, array('none', 'capitalize', 'uppercase', 'lowercase'))) {
                        $val = 'none';
                    }
                    $this->value['text-transform']  = $val;
                    break;

                case 'font-size':
                    $val = $this->cssConverter->convertFontSize($val, $this->value['font-size']);
                    if ($val) {
                        $this->value['font-size'] = $val;
                    }
                    break;

                case 'color':
                    $res = null;
                    $this->value['color'] = $this->cssConverter->convertToColor($val, $res);
                    if ($tagName === 'hr') {
                        $this->value['border']['l']['color'] = $this->value['color'];
                        $this->value['border']['t']['color'] = $this->value['color'];
                        $this->value['border']['r']['color'] = $this->value['color'];
                        $this->value['border']['b']['color'] = $this->value['color'];
                    }
                    break;

                case 'text-align':
                    $val = strtolower($val);
                    if (!in_array($val, array('left', 'right', 'center', 'justify', 'li_right'))) {
                        $val = 'left';
                    }
                    $this->value['text-align'] = $val;
                    break;

                case 'vertical-align':
                    $this->value['vertical-align'] = $val;
                    break;

                case 'width':
                    $this->value['width'] = $this->cssConverter->convertToMM($val, $this->getLastWidth());
                    if ($this->value['width'] && substr($val, -1) === '%') {
                        $correctWidth=true;
                    }
                    $noWidth = false;
                    break;

                case 'max-width':
                    $this->value[$nom] = $this->cssConverter->convertToMM($val, $this->getLastWidth());
                    break;

                case 'height':
                case 'max-height':
                    $this->value[$nom] = $this->cssConverter->convertToMM($val, $this->getLastHeight());
                    break;

                case 'line-height':
                    if (preg_match('/^[0-9\.]+$/isU', $val)) {
                        $val = floor($val*100).'%';
                    }
                    $this->value['line-height'] = $val;
                    break;

                case 'rotate':
                    if (!in_array($val, array(0, -90, 90, 180, 270, -180, -270))) {
                        $val = null;
                    }
                    if ($val<0) {
                        $val+= 360;
                    }
                    $this->value['rotate'] = $val;
                    break;

                case 'overflow':
                    if (!in_array($val, array('visible', 'hidden'))) {
                        $val = 'visible';
                    }
                    $this->value['overflow'] = $val;
                    break;

                case 'padding':
                    $val = explode(' ', $val);
                    foreach ($val as $k => $v) {
                        $v = trim($v);
                        if ($v !== '') {
                            $val[$k] = $v;
                        } else {
                            unset($val[$k]);
                        }
                    }
                    $val = array_values($val);
                    $this->duplicateBorder($val);
                    $this->value['padding']['t'] = $this->cssConverter->convertToMM($val[0], 0);
                    $this->value['padding']['r'] = $this->cssConverter->convertToMM($val[1], 0);
                    $this->value['padding']['b'] = $this->cssConverter->convertToMM($val[2], 0);
                    $this->value['padding']['l'] = $this->cssConverter->convertToMM($val[3], 0);
                    break;

                case 'padding-top':
                    $this->value['padding']['t'] = $this->cssConverter->convertToMM($val, 0);
                    break;

                case 'padding-right':
                    $this->value['padding']['r'] = $this->cssConverter->convertToMM($val, 0);
                    break;

                case 'padding-bottom':
                    $this->value['padding']['b'] = $this->cssConverter->convertToMM($val, 0);
                    break;

                case 'padding-left':
                    $this->value['padding']['l'] = $this->cssConverter->convertToMM($val, 0);
                    break;

                case 'margin':
                    if ($val === 'auto') {
                        $this->value['margin-auto'] = true;
                        break;
                    }
                    $val = explode(' ', $val);
                    foreach ($val as $k => $v) {
                        $v = trim($v);
                        if ($v !== '') {
                            $val[$k] = $v;
                        } else {
                            unset($val[$k]);
                        }
                    }
                    $val = array_values($val);
                    $this->duplicateBorder($val);
                    $this->value['margin']['t'] = $this->cssConverter->convertToMM($val[0], $this->getLastHeight());
                    $this->value['margin']['r'] = $this->cssConverter->convertToMM($val[1], $this->getLastWidth());
                    $this->value['margin']['b'] = $this->cssConverter->convertToMM($val[2], $this->getLastHeight());
                    $this->value['margin']['l'] = $this->cssConverter->convertToMM($val[3], $this->getLastWidth());
                    break;

                case 'margin-top':
                    $this->value['margin']['t'] = $this->cssConverter->convertToMM($val, $this->getLastHeight());
                    break;

                case 'margin-right':
                    $this->value['margin']['r'] = $this->cssConverter->convertToMM($val, $this->getLastWidth());
                    break;

                case 'margin-bottom':
                    $this->value['margin']['b'] = $this->cssConverter->convertToMM($val, $this->getLastHeight());
                    break;

                case 'margin-left':
                    $this->value['margin']['l'] = $this->cssConverter->convertToMM($val, $this->getLastWidth());
                    break;

                case 'border':
                    $val = $this->readBorder($val);
                    $this->value['border']['t'] = $val;
                    $this->value['border']['r'] = $val;
                    $this->value['border']['b'] = $val;
                    $this->value['border']['l'] = $val;
                    break;

                case 'border-style':
                    $val = explode(' ', $val);
                    foreach ($val as $valK => $valV) {
                        if (!in_array($valV, array('solid', 'dotted', 'dashed'))) {
                            $val[$valK] = null;
                        }
                    }
                    $this->duplicateBorder($val);
                    if ($val[0]) {
                        $this->value['border']['t']['type'] = $val[0];
                    }
                    if ($val[1]) {
                        $this->value['border']['r']['type'] = $val[1];
                    }
                    if ($val[2]) {
                        $this->value['border']['b']['type'] = $val[2];
                    }
                    if ($val[3]) {
                        $this->value['border']['l']['type'] = $val[3];
                    }
                    break;

                case 'border-top-style':
                    if (in_array($val, array('solid', 'dotted', 'dashed'))) {
                        $this->value['border']['t']['type'] = $val;
                    }
                    break;

                case 'border-right-style':
                    if (in_array($val, array('solid', 'dotted', 'dashed'))) {
                        $this->value['border']['r']['type'] = $val;
                    }
                    break;

                case 'border-bottom-style':
                    if (in_array($val, array('solid', 'dotted', 'dashed'))) {
                        $this->value['border']['b']['type'] = $val;
                    }
                    break;

                case 'border-left-style':
                    if (in_array($val, array('solid', 'dotted', 'dashed'))) {
                        $this->value['border']['l']['type'] = $val;
                    }
                    break;

                case 'border-color':
                    $res = false;
                    $val = preg_replace('/,[\s]+/', ',', $val);
                    $val = explode(' ', $val);
                    foreach ($val as $valK => $valV) {
                            $val[$valK] = $this->cssConverter->convertToColor($valV, $res);
                        if (!$res) {
                            $val[$valK] = null;
                        }
                    }
                    $this->duplicateBorder($val);
                    if (is_array($val[0])) {
                        $this->value['border']['t']['color'] = $val[0];
                    }
                    if (is_array($val[1])) {
                        $this->value['border']['r']['color'] = $val[1];
                    }
                    if (is_array($val[2])) {
                        $this->value['border']['b']['color'] = $val[2];
                    }
                    if (is_array($val[3])) {
                        $this->value['border']['l']['color'] = $val[3];
                    }

                    break;

                case 'border-top-color':
                    $res = false;
                    $val = $this->cssConverter->convertToColor($val, $res);
                    if ($res) {
                        $this->value['border']['t']['color'] = $val;
                    }
                    break;

                case 'border-right-color':
                    $res = false;
                    $val = $this->cssConverter->convertToColor($val, $res);
                    if ($res) {
                        $this->value['border']['r']['color'] = $val;
                    }
                    break;

                case 'border-bottom-color':
                    $res = false;
                    $val = $this->cssConverter->convertToColor($val, $res);
                    if ($res) {
                        $this->value['border']['b']['color'] = $val;
                    }
                    break;

                case 'border-left-color':
                    $res = false;
                    $val = $this->cssConverter->convertToColor($val, $res);
                    if ($res) {
                        $this->value['border']['l']['color'] = $val;
                    }
                    break;

                case 'border-width':
                    $val = explode(' ', $val);
                    foreach ($val as $valK => $valV) {
                            $val[$valK] = $this->cssConverter->convertToMM($valV, 0);
                    }
                    $this->duplicateBorder($val);
                    if ($val[0]) {
                        $this->value['border']['t']['width'] = $val[0];
                    }
                    if ($val[1]) {
                        $this->value['border']['r']['width'] = $val[1];
                    }
                    if ($val[2]) {
                        $this->value['border']['b']['width'] = $val[2];
                    }
                    if ($val[3]) {
                        $this->value['border']['l']['width'] = $val[3];
                    }
                    break;

                case 'border-top-width':
                    $val = $this->cssConverter->convertToMM($val, 0);
                    if ($val) {
                        $this->value['border']['t']['width'] = $val;
                    }
                    break;

                case 'border-right-width':
                    $val = $this->cssConverter->convertToMM($val, 0);
                    if ($val) {
                        $this->value['border']['r']['width'] = $val;
                    }
                    break;

                case 'border-bottom-width':
                    $val = $this->cssConverter->convertToMM($val, 0);
                    if ($val) {
                        $this->value['border']['b']['width'] = $val;
                    }
                    break;

                case 'border-left-width':
                    $val = $this->cssConverter->convertToMM($val, 0);
                    if ($val) {
                        $this->value['border']['l']['width'] = $val;
                    }
                    break;

                case 'border-collapse':
                    if ($tagName === 'table') {
                        $this->value['border']['collapse'] = ($val === 'collapse');
                    }
                    break;

                case 'border-radius':
                    $val = explode('/', $val);
                    if (count($val)>2) {
                        break;
                    }
                    $valH = $this->cssConverter->convertToRadius(trim($val[0]));
                    if (count($valH)<1 || count($valH)>4) {
                        break;
                    }
                    if (!isset($valH[1])) {
                        $valH[1] = $valH[0];
                    }
                    if (!isset($valH[2])) {
                        $valH = array($valH[0], $valH[0], $valH[1], $valH[1]);
                    }
                    if (!isset($valH[3])) {
                        $valH[3] = $valH[1];
                    }
                    if (isset($val[1])) {
                        $valV = $this->cssConverter->convertToRadius(trim($val[1]));
                        if (count($valV)<1 || count($valV)>4) {
                            break;
                        }
                        if (!isset($valV[1])) {
                            $valV[1] = $valV[0];
                        }
                        if (!isset($valV[2])) {
                            $valV = array($valV[0], $valV[0], $valV[1], $valV[1]);
                        }
                        if (!isset($valV[3])) {
                            $valV[3] = $valV[1];
                        }
                    } else {
                        $valV = $valH;
                    }
                    $this->value['border']['radius'] = array(
                                'tl' => array($valH[0], $valV[0]),
                                'tr' => array($valH[1], $valV[1]),
                                'br' => array($valH[2], $valV[2]),
                                'bl' => array($valH[3], $valV[3])
                            );
                    break;

                case 'border-top-left-radius':
                    $val = $this->cssConverter->convertToRadius($val);
                    if (count($val)<1 || count($val)>2) {
                        break;
                    }
                    $this->value['border']['radius']['tl'] = array($val[0], isset($val[1]) ? $val[1] : $val[0]);
                    break;

                case 'border-top-right-radius':
                    $val = $this->cssConverter->convertToRadius($val);
                    if (count($val)<1 || count($val)>2) {
                        break;
                    }
                    $this->value['border']['radius']['tr'] = array($val[0], isset($val[1]) ? $val[1] : $val[0]);
                    break;

                case 'border-bottom-right-radius':
                    $val = $this->cssConverter->convertToRadius($val);
                    if (count($val)<1 || count($val)>2) {
                        break;
                    }
                    $this->value['border']['radius']['br'] = array($val[0], isset($val[1]) ? $val[1] : $val[0]);
                    break;

                case 'border-bottom-left-radius':
                    $val = $this->cssConverter->convertToRadius($val);
                    if (count($val)<1 || count($val)>2) {
                        break;
                    }
                    $this->value['border']['radius']['bl'] = array($val[0], isset($val[1]) ? $val[1] : $val[0]);
                    break;

                case 'border-top':
                    $this->value['border']['t'] = $this->readBorder($val);
                    break;

                case 'border-right':
                    $this->value['border']['r'] = $this->readBorder($val);
                    break;

                case 'border-bottom':
                    $this->value['border']['b'] = $this->readBorder($val);
                    break;

                case 'border-left':
                    $this->value['border']['l'] = $this->readBorder($val);
                    break;

                case 'background-color':
                    $this->value['background']['color'] = $this->cssConverter->convertBackgroundColor($val);
                    break;

                case 'background-image':
                    $this->value['background']['image'] = $this->cssConverter->convertBackgroundImage($val);
                    break;

                case 'background-position':
                    $res = null;
                    $this->value['background']['position'] = $this->cssConverter->convertBackgroundPosition($val, $res);
                    break;

                case 'background-repeat':
                    $this->value['background']['repeat'] = $this->cssConverter->convertBackgroundRepeat($val);
                    break;

                case 'background':
                    $this->cssConverter->convertBackground($val, $this->value['background']);
                    break;

                case 'position':
                    if ($val === 'absolute') {
                        $this->value['position'] = 'absolute';
                    } elseif ($val === 'relative') {
                        $this->value['position'] = 'relative';
                    } else {
                        $this->value['position'] = null;
                    }
                    break;

                case 'float':
                    if ($val === 'left') {
                        $this->value['float'] = 'left';
                    } elseif ($val === 'right') {
                        $this->value['float'] = 'right';
                    } else {
                        $this->value['float'] = null;
                    }
                    break;

                case 'display':
                    if ($val === 'inline') {
                        $this->value['display'] = 'inline';
                    } elseif ($val === 'block') {
                        $this->value['display'] = 'block';
                    } elseif ($val === 'none') {
                        $this->value['display'] = 'none';
                    } else {
                        $this->value['display'] = null;
                    }
                    break;

                case 'top':
                case 'bottom':
                case 'left':
                case 'right':
                    $this->value[$nom] = $val;
                    break;

                case 'list-style':
                case 'list-style-type':
                case 'list-style-image':
                    if ($nom === 'list-style') {
                        $nom = 'list-style-type';
                    }
                    $this->value[$nom] = $val;
                    break;

                case 'page-break-before':
                case 'page-break-after':
                    $this->value[$nom] = $val;
                    break;

                case 'start':
                    $this->value[$nom] = intval($val);
                    break;

                default:
                    break;
            }
        }

        $return = true;

        // only for P tag
        if ($this->value['margin']['t'] === null) {
            $this->value['margin']['t'] = $this->value['font-size'];
        }
        if ($this->value['margin']['b'] === null) {
            $this->value['margin']['b'] = $this->value['font-size'];
        }

        // force the text align to left, if asked by html2pdf
        if ($this->onlyLeft) {
            $this->value['text-align'] = 'left';
        }

        // correction on the width (quick box)
        if ($noWidth
            && in_array($tagName, array('div', 'blockquote', 'fieldset'))
            && $this->value['position'] !== 'absolute'
        ) {
            $this->value['width'] = $this->getLastWidth();
            $this->value['width']-= $this->value['margin']['l'] + $this->value['margin']['r'];
        } else {
            if ($correctWidth) {
                if (!in_array($tagName, array('table', 'div', 'blockquote', 'fieldset', 'hr'))) {
                    $this->value['width']-= $this->value['padding']['l'] + $this->value['padding']['r'];
                    $this->value['width']-= $this->value['border']['l']['width'] + $this->value['border']['r']['width'];
                }
                if (in_array($tagName, array('th', 'td'))) {
                    $this->value['width']-= $this->cssConverter->convertToMM(isset($param['cellspacing']) ? $param['cellspacing'] : '2px');
                    $return = false;
                }
                if ($this->value['width']<0) {
                    $this->value['width']=0;
                }
            } else {
                if ($this->value['width']) {
                    if ($this->value['border']['l']['width']) {
                        $this->value['width'] += $this->value['border']['l']['width'];
                    }
                    if ($this->value['border']['r']['width']) {
                        $this->value['width'] += $this->value['border']['r']['width'];
                    }
                    if ($this->value['padding']['l']) {
                        $this->value['width'] += $this->value['padding']['l'];
                    }
                    if ($this->value['padding']['r']) {
                        $this->value['width'] += $this->value['padding']['r'];
                    }
                }
            }
        }
        if ($this->value['height']) {
            if ($this->value['border']['b']['width']) {
                $this->value['height'] += $this->value['border']['b']['width'];
            }
            if ($this->value['border']['t']['width']) {
                $this->value['height'] += $this->value['border']['t']['width'];
            }
            if ($this->value['padding']['b']) {
                $this->value['height'] += $this->value['padding']['b'];
            }
            if ($this->value['padding']['t']) {
                $this->value['height'] += $this->value['padding']['t'];
            }
        }

        if ($this->value['top'] != null) {
            $this->value['top']     = $this->cssConverter->convertToMM($this->value['top'], $this->getLastHeight(true));
        }
        if ($this->value['bottom'] != null) {
            $this->value['bottom']  = $this->cssConverter->convertToMM($this->value['bottom'], $this->getLastHeight(true));
        }
        if ($this->value['left'] != null) {
            $this->value['left']    = $this->cssConverter->convertToMM($this->value['left'], $this->getLastWidth(true));
        }
        if ($this->value['right'] != null) {
            $this->value['right']   = $this->cssConverter->convertToMM($this->value['right'], $this->getLastWidth(true));
        }

        if ($this->value['top'] && $this->value['bottom'] && $this->value['height']) {
            $this->value['bottom']  = null;
        }
        if ($this->value['left'] && $this->value['right'] && $this->value['width']) {
            $this->value['right']   = null;
        }

        return $return;
    }

    /**
     * Get the height of the current line
     *
     * @return float height in mm
     */
    public function getLineHeight()
    {
        $val = $this->value['line-height'];
        if ($val === 'normal') {
            $val = '108%';
        }
        return $this->cssConverter->convertToMM($val, $this->value['font-size']);
    }

    /**
     * Get the width of the parent
     *
     * @param  boolean $mode true => adding padding and border
     *
     * @return float width in mm
     */
    public function getLastWidth($mode = false)
    {
        for ($k=count($this->table)-1; $k>=0; $k--) {
            if ($this->table[$k]['width']) {
                $w = $this->table[$k]['width'];
                if ($mode) {
                    $w+= $this->table[$k]['border']['l']['width'] + $this->table[$k]['padding']['l'] + 0.02;
                    $w+= $this->table[$k]['border']['r']['width'] + $this->table[$k]['padding']['r'] + 0.02;
                }
                return $w;
            }
        }
        return $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();
    }

    /**
     * Get the height of the parent
     *
     * @param  boolean $mode true => adding padding and border
     *
     * @return float height in mm
     */
    public function getLastHeight($mode = false)
    {
        for ($k=count($this->table)-1; $k>=0; $k--) {
            if ($this->table[$k]['height']) {
                $h = $this->table[$k]['height'];
                if ($mode) {
                    $h+= $this->table[$k]['border']['t']['width'] + $this->table[$k]['padding']['t'] + 0.02;
                    $h+= $this->table[$k]['border']['b']['width'] + $this->table[$k]['padding']['b'] + 0.02;
                }
                return $h;
            }
        }
        return $this->pdf->getH() - $this->pdf->gettMargin() - $this->pdf->getbMargin();
    }

    /**
     * Get the value of the float property
     *
     * @return string left/right
     */
    public function getFloat()
    {
        if ($this->value['float'] === 'left') {
            return 'left';
        }
        if ($this->value['float'] === 'right') {
            return 'right';
        }
        return null;
    }

    /**
     * Get the last value for a specific key
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function getLastValue($key)
    {
        $nb = count($this->table);
        if ($nb>0) {
            return $this->table[$nb-1][$key];
        } else {
            return null;
        }
    }

    /**
     * Get the last absolute X
     *
     * @return float x
     */
    protected function getLastAbsoluteX()
    {
        for ($k=count($this->table)-1; $k>=0; $k--) {
            if ($this->table[$k]['x'] && $this->table[$k]['position']) {
                return $this->table[$k]['x'];
            }
        }
        return $this->pdf->getlMargin();
    }

    /**
     * Get the last absolute Y
     *
     * @return float y
     */
    protected function getLastAbsoluteY()
    {
        for ($k=count($this->table)-1; $k>=0; $k--) {
            if ($this->table[$k]['y'] && $this->table[$k]['position']) {
                return $this->table[$k]['y'];
            }
        }
        return $this->pdf->gettMargin();
    }

    /**
     * Get the CSS properties of the current tag
     *
     * @return array styles
     */
    protected function getFromCSS()
    {
        // styles to apply
        $styles = array();

        // list of the selectors to get in the CSS files
        $getit  = array();

        // get the list of the selectors of each tags
        $lst = array();
        $lst[] = $this->value['id_lst'];
        for ($i=count($this->table)-1; $i>=0; $i--) {
            $lst[] = $this->table[$i]['id_lst'];
        }

        // foreach selectors in the CSS files, verify if it match with the list of selectors
        foreach ($this->cssKeys as $key => $num) {
            if ($this->getReccursiveStyle($key, $lst)) {
                $getit[$key] = $num;
            }
        }

        // if we have selectors
        if (count($getit)) {
            // get them, but in the definition order, because of priority
            asort($getit);
            foreach ($getit as $key => $val) {
                $styles = array_merge($styles, $this->css[$key]);
            }
        }

        return $styles;
    }

    /**
     * Identify if the selector $key match with the list of tag selectors
     *
     * @param  string   $key CSS selector to analyse
     * @param  array    $lst list of the selectors of each tags
     * @param  string   $next next step of parsing the selector
     *
     * @return boolean
     */
    protected function getReccursiveStyle($key, $lst, $next = null)
    {
        // if next step
        if ($next !== null) {
            // we remove this step
            if ($next) {
                $key = trim(substr($key, 0, -strlen($next)));
            }
            array_shift($lst);

            // if no more step to identify => return false
            if (!count($lst)) {
                return false;
            }
        }

        // for each selector of the current step
        foreach ($lst[0] as $name) {
            // if selector = key => ok
            if ($key == $name) {
                return true;
            }

            // if the end of the key = the selector and the next step is ok => ok
            if (substr($key, -strlen(' '.$name)) === ' '.$name && $this->getReccursiveStyle($key, $lst, $name)) {
                return true;
            }
        }

        // if we are not in the first step, we analyse the sub steps (the pareng tag of the current tag)
        if ($next !== null && $this->getReccursiveStyle($key, $lst, '')) {
            return true;
        }

        // no corresponding found
        return false;
    }

    /**
     * Analyse a border
     *
     * @param   string $css css border properties
     *
     * @return  array border properties
     */
    public function readBorder($css)
    {
        // border none
        $none = array('type' => 'none', 'width' => 0, 'color' => array(0, 0, 0));

        // default value
        $type  = 'solid';
        $width = $this->cssConverter->convertToMM('1pt');
        $color = array(0, 0, 0);

        // clean up the values
        $css = explode(' ', $css);
        foreach ($css as $k => $v) {
            $v = trim($v);
            if ($v !== '') {
                $css[$k] = $v;
            } else {
                unset($css[$k]);
            }
        }
        $css = array_values($css);

        // read the values
        $res = null;
        foreach ($css as $value) {

            // if no border => return none
            if ($value === 'none' || $value === 'hidden') {
                return $none;
            }

            // try to convert the value as a distance
            $tmp = $this->cssConverter->convertToMM($value);

            // if the convert is ok => it is a width
            if ($tmp !== null) {
                $width = $tmp;
            // else, it could be the type
            } elseif (in_array($value, array('solid', 'dotted', 'dashed', 'double'))) {
                $type = $value;
            // else, it could be the color
            } else {
                $tmp = $this->cssConverter->convertToColor($value, $res);
                if ($res) {
                    $color = $tmp;
                }
            }
        }

        // if no witdh => return none
        if (!$width) {
            return $none;
        }

        // return the border properties
        return array('type' => $type, 'width' => $width, 'color' => $color);
    }

    /**
     * Duplicate the borders if needed
     *
     * @param  &array $val
     *
     * @return void
     */
    protected function duplicateBorder(&$val)
    {
        // 1 value => L => RTB
        if (count($val) == 1) {
            $val[1] = $val[0];
            $val[2] = $val[0];
            $val[3] = $val[0];
        // 2 values => L => R & T => B
        } elseif (count($val) == 2) {
            $val[2] = $val[0];
            $val[3] = $val[1];
        // 3 values => T => B
        } elseif (count($val) == 3) {
            $val[3] = $val[1];
        }
    }


    /**
     * Read a css content
     *
     * @param  &string $code
     *
     * @return void
     */
    protected function analyseStyle($code)
    {
        // clean the spaces
        $code = preg_replace('/[\s]+/', ' ', $code);

        // remove the comments
        $code = preg_replace('/\/\*.*?\*\//s', '', $code);

        // split each CSS code "selector { value }"
        preg_match_all('/([^{}]+){([^}]*)}/isU', $code, $match);

        // for each CSS code
        $amountMatch = count($match[0]);
        for ($k = 0; $k < $amountMatch; $k++) {

            // selectors
            $names = strtolower(trim($match[1][$k]));

            // css style
            $styles = trim($match[2][$k]);

            // explode each value
            $styles = explode(';', $styles);

            // parse each value
            $css = array();
            foreach ($styles as $style) {
                $tmp = explode(':', $style);
                if (count($tmp) > 1) {
                    $cod = $tmp[0];
                    unset($tmp[0]);
                    $tmp = implode(':', $tmp);
                    $css[trim(strtolower($cod))] = trim($tmp);
                }
            }

            // explode the names
            $names = explode(',', $names);

            // save the values for each names
            foreach ($names as $name) {
                // clean the name
                $name = trim($name);

                // if a selector with something like :hover => continue
                if (strpos($name, ':') !== false) {
                    continue;
                }

                // save the value
                if (!isset($this->css[$name])) {
                    $this->css[$name] = $css;
                } else {
                    $this->css[$name] = array_merge($this->css[$name], $css);
                }

            }
        }

        // get the list of the keys
        $this->cssKeys = array_flip(array_keys($this->css));
    }

    /**
     * Extract the css files from a html code
     *
     * @param  string $html
     *
     * @return string
     */
    public function extractStyle($html)
    {
        // the CSS content
        $style = ' ';

        // extract the link tags, and remove them in the html code
        preg_match_all('/<link([^>]*)>/isU', $html, $match);
        $html = preg_replace('/<link[^>]*>/isU', '', $html);
        $html = preg_replace('/<\/link[^>]*>/isU', '', $html);

        // analyse each link tag
        foreach ($match[1] as $code) {
            $tmp = $this->tagParser->extractTagAttributes($code);

            // if type text/css => we keep it
            if (isset($tmp['type']) && strtolower($tmp['type']) === 'text/css' && isset($tmp['href'])) {

                // get the href
                $url = $tmp['href'];

                // get the content of the css file
                $this->checkValidPath($url);
                $content = @file_get_contents($url);

                // if "http://" in the url
                if (strpos($url, 'http://') !== false) {

                    // get the domain "http://xxx/"
                    $url = str_replace('http://', '', $url);
                    $url = explode('/', $url);
                    $urlMain = 'http://'.$url[0].'/';

                    // get the absolute url of the path
                    $urlSelf = $url;
                    unset($urlSelf[count($urlSelf)-1]);
                    $urlSelf = 'http://'.implode('/', $urlSelf).'/';

                    // adapt the url in the css content
                    $content = preg_replace('/url\(([^\\\\][^)]*)\)/isU', 'url('.$urlSelf.'$1)', $content);
                    $content = preg_replace('/url\((\\\\[^)]*)\)/isU', 'url('.$urlMain.'$1)', $content);
                } else {
                    // @TODO correction on url in absolute on a local css content
                    // $content = preg_replace('/url\(([^)]*)\)/isU', 'url('.dirname($url).'/$1)', $content);
                }

                // add to the CSS content
                $style.= $content."\n";
            }
        }

        // extract the style tags des tags style, and remove them in the html code
        preg_match_all('/<style[^>]*>(.*)<\/style[^>]*>/isU', $html, $match);
        $html = preg_replace_callback('/<style[^>]*>(.*)<\/style[^>]*>/isU', [$this, 'removeStyleTag'], $html);

        // analyse each style tags
        foreach ($match[1] as $code) {
            // add to the CSS content
            $code = str_replace('<!--', '', $code);
            $code = str_replace('-->', '', $code);
            $style.= $code."\n";
        }

        //analyse the css content
        $this->analyseStyle($style);

        return $html;
    }

    /**
     * put the same line number for the lexer
     * @param string[] $match
     * @return string
     */
    private function removeStyleTag(array $match)
    {
        $nbLines = count(explode("\n", $match[0]))-1;

        return str_pad('', $nbLines, "\n");
    }

    /**
     * @param string $path
     * @return void
     * @throws HtmlParsingException
     */
    public function checkValidPath($path)
    {
        $path = trim(strtolower($path));
        $scheme = parse_url($path, PHP_URL_SCHEME);

        if ($scheme === null) {
            return;
        }

        if (in_array($scheme, $this->authorizedSchemes)) {
            return;
        }

        if (strlen($scheme) === 1 && preg_match('/^[a-z]$/i', $scheme)) {
            return;
        }

        throw new HtmlParsingException('Unauthorized path scheme');
    }
}
