<?php
/**
 * Html2Pdf Library - parsing Html class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Parsing;

use Spipu\Html2Pdf\Exception\HtmlParsingException;

/**
 * Class TagParser
 */
class TagParser
{
    protected $level;         // table level
    protected $num = 0;       // table number
    protected $textParser;

    public function __construct(TextParser $textParser)
    {
        $this->textParser = $textParser;
        $this->level = array($this->num);
    }

    /**
     * analyze a HTML tag
     *
     * @param string $code HTML code to analyze
     *
     * @return Node corresponding action
     * @throws HtmlParsingException
     */
    public function analyzeTag($code)
    {
        // name of the tag, opening, closure, autoclosure
        $tag = '<([\/]{0,1})([_a-z0-9]+)([\/>\s]+)';
        if (!preg_match('/'.$tag.'/isU', $code, $match)) {
            $e = new HtmlParsingException('The HTML tag ['.$code.'] provided is invalid');
            $e->setInvalidTag($code);
            throw $e;
        }
        $close     = ($match[1] === '/' ? true : false);
        $autoclose = preg_match('/\/>$/isU', $code);
        $name      = strtolower($match[2]);

        // required parameters (depends on the tag name)
        $defaultParams = array();
        $defaultParams['style'] = '';
        if ($name === 'img') {
            $defaultParams['alt'] = '';
            $defaultParams['src'] = '';
        } elseif ($name === 'a') {
            $defaultParams['href'] = '';
        }

        $param = array_merge($defaultParams, $this->extractTagAttributes($code));
        $param['style'] = trim($param['style']);
        if (strlen($param['style']) > 0 && substr($param['style'], -1) !== ';') {
            $param['style'].= ';';
        }

        // compliance of each parameter
        $color  = "#000000";
        $border = null;
        foreach ($param as $key => $val) {
            switch ($key) {
                case 'width':
                    unset($param[$key]);
                    $param['style'] .= 'width: '.$val.'px; ';
                    break;

                case 'align':
                    if ($name === 'img') {
                        unset($param[$key]);
                        $param['style'] .= 'float: '.$val.'; ';
                    } elseif ($name !== 'table') {
                        unset($param[$key]);
                        $param['style'] .= 'text-align: '.$val.'; ';
                    }
                    break;

                case 'valign':
                    unset($param[$key]);
                    $param['style'] .= 'vertical-align: '.$val.'; ';
                    break;

                case 'height':
                    unset($param[$key]);
                    $param['style'] .= 'height: '.$val.'px; ';
                    break;

                case 'bgcolor':
                    unset($param[$key]);
                    $param['style'] .= 'background: '.$val.'; ';
                    break;

                case 'bordercolor':
                    unset($param[$key]);
                    $color = $val;
                    break;

                case 'border':
                    unset($param[$key]);
                    if (preg_match('/^[0-9]+$/isU', $val)) {
                        $val = $val.'px';
                    }
                    $border = $val;
                    break;

                case 'cellpadding':
                case 'cellspacing':
                    if (preg_match('/^([0-9]+)$/isU', $val)) {
                        $param[$key] = $val.'px';
                    }
                    break;

                case 'colspan':
                case 'rowspan':
                    $val = preg_replace('/[^0-9]/isU', '', $val);
                    if (!$val) {
                        $val = 1;
                    }
                    $param[$key] = (int) $val;
                    break;

                case 'color':
                    if ($name === 'font') {
                        unset($param[$key]);
                        $param['style'] .= 'color: '.$val.'; ';
                    }
                    break;
            }
        }

        // compliance of the border
        if ($border !== null) {
            if ($border && $border !== '0px') {
                $border = 'solid '.$border.' '.$color;
            } else {
                $border = 'none';
            }

            $param['style'] .= 'border: '.$border.'; ';
            $param['border'] = $border;
        }

        // reading styles: decomposition and standardization
        $styles = explode(';', $param['style']);
        $param['style'] = array();
        foreach ($styles as $style) {
            $tmp = explode(':', $style);
            if (count($tmp) > 1) {
                $cod = $tmp[0];
                unset($tmp[0]);
                $tmp = implode(':', $tmp);
                $param['style'][trim(strtolower($cod))] = preg_replace('/[\s]+/isU', ' ', trim($tmp));
            }
        }

        // determining the level of table opening, with an added level
        if (in_array($name, array('ul', 'ol', 'table')) && !$close) {
            $this->num++;
            array_push($this->level, $this->num);
        }

        // get the level of the table containing the element
        if (!isset($param['num'])) {
            $param['num'] = end($this->level);
        }

        // for closures table: remove a level
        if (in_array($name, array('ul', 'ol', 'table')) && $close) {
            array_pop($this->level);
        }

        // prepare the parameters
        if (isset($param['value'])) {
            $keepSpaces = in_array($name, array('qrcode', 'barcode'));
            $param['value']  = $this->textParser->prepareTxt($param['value'], !$keepSpaces);
        }
        if (isset($param['alt'])) {
            $param['alt']    = $this->textParser->prepareTxt($param['alt']);
        }
        if (isset($param['title'])) {
            $param['title']  = $this->textParser->prepareTxt($param['title']);
        }
        if (isset($param['class'])) {
            $param['class']  = $this->textParser->prepareTxt($param['class']);
        }

        // return the new action to do
        return new Node($name, $param, $close, $autoclose);
    }

    /**
     * Extract the list of attribute => value inside an HTML tag
     *
     * @param string $code The full HTML tag to parse
     *
     * @return array
     */
    public function extractTagAttributes($code)
    {
        $param = array();
        $regexes = array(
            '([a-zA-Z0-9_]+)=([^"\'\s>]+)',  // read the parameters : name=value
            '([a-zA-Z0-9_]+)=\s*["]([^"]*)["]', // read the parameters : name="value"
            "([a-zA-Z0-9_]+)=\s*[']([^']*)[']"  // read the parameters : name='value'
        );

        foreach ($regexes as $regex) {
            preg_match_all('/'.$regex.'/is', $code, $match);
            $amountMatch = count($match[0]);
            for ($k = 0; $k < $amountMatch; $k++) {
                $param[trim(strtolower($match[1][$k]))] = trim($match[2][$k]);
            }
        }

        return $param;
    }
}
