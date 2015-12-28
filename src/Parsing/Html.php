<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Parsing;

use Spipu\Html2Pdf\Exception\HtmlParsingException;

/**
 * Class Html
 */
class Html
{
    protected $tagParser;
    protected $textParser;
    protected $tagPreIn;
    protected $_encoding = '';        // encoding
    public $code      = array();   // parsed HTML code

    const HTML_TAB = '        ';

    /**
     * main constructor
     *
     * @param TextParser $textParser
     */
    public function __construct(TextParser $textParser)
    {
        $this->textParser = $textParser;
        $this->tagParser = new TagParser($this->textParser);
        $this->code  = array();
    }

    /**
     * parse the HTML code
     *
     * @param array $tokens A list of tokens to parse
     *
     * @throws HtmlParsingException
     */
    public function parse($tokens)
    {
        $parents = array();

        // flag : are we in a <pre> Tag ?
        $this->tagPreIn = false;

        // all the actions to do
        $actions = array();

        // get the actions from the html tokens
        foreach ($tokens as $token) {
            if ($token->getType() == 'code') {
                $actions = array_merge($actions, $this->getTagAction($token, $parents));
            } elseif ($token->getType() == 'txt') {
                $actions = array_merge($actions, $this->getTextAction($token));
            }
        }

        // for each identified action, we have to clean up the begin and the end of the texte
        // based on tags that surround it

        // list of the tags to clean
        $tagsToClean = array(
            'page', 'page_header', 'page_footer', 'form',
            'table', 'thead', 'tfoot', 'tr', 'td', 'th', 'br',
            'div', 'hr', 'p', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'bookmark', 'fieldset', 'legend',
            'draw', 'circle', 'ellipse', 'path', 'rect', 'line', 'g', 'polygon', 'polyline',
            'option'
        );

        // foreach action
        $nb = count($actions);
        for ($k = 0; $k < $nb; $k++) {
            // if it is a Text
            if ($actions[$k]['name']=='write') {
                // if the tag before the text is a tag to clean => ltrim on the text
                if ($k>0 && in_array($actions[$k - 1]['name'], $tagsToClean)) {
                    $actions[$k]['param']['txt'] = ltrim($actions[$k]['param']['txt']);
                }

                // if the tag after the text is a tag to clean => rtrim on the text
                if ($k < $nb - 1 && in_array($actions[$k + 1]['name'], $tagsToClean)) {
                    $actions[$k]['param']['txt'] = rtrim($actions[$k]['param']['txt']);
                }

                // if the text is empty => remove the action
                if (!strlen($actions[$k]['param']['txt'])) {
                    unset($actions[$k]);
                }
            }
        }

        // if we are not on the level 0 => HTML validator ERROR
        if (count($parents)) {
            if (count($parents)>1) {
                $errorMsg = 'The following tags have not been closed:';
            } else {
                $errorMsg = 'The following tag has not been closed:';
            }

            $e = new HtmlParsingException($errorMsg.' '.implode(', ', $parents));
            $e->setInvalidTag($parents[0]);
            throw $e;
        }

        // save the actions to do
        $this->code = array_values($actions);
    }

    /**
     * TODO remove the reference on the $parents variable
     *
     * @param Token $token
     * @param array $parents
     *
     * @return array
     * @throws HtmlParsingException
     */
    protected function getTagAction(Token $token, &$parents)
    {
        // tag that can be not closed
        $tagsNotClosed = array(
            'br', 'hr', 'img', 'col',
            'input', 'link', 'option',
            'circle', 'ellipse', 'path', 'rect', 'line', 'polygon', 'polyline'
        );

        // analyze the HTML code
        $res = $this->tagParser->analyzeTag($token->getData());

        // save the current position in the HTML code
        $res['line'] = $token->getLine();

        $actions = array();
        // if the tag must be closed
        if (!in_array($res['name'], $tagsNotClosed)) {
            // if it is a closure tag
            if ($res['close']) {
                // HTML validation
                if (count($parents) < 1) {
                    $e = new HtmlParsingException('Too many tag closures found for ['.$res['name'].']');
                    $e->setInvalidTag($res['name']);
                    $e->setHtmlLine($res['line']);
                    throw $e;
                } elseif (end($parents) != $res['name']) {
                    $e = new HtmlParsingException('Tags are closed in a wrong order for ['.$res['name'].']');
                    $e->setInvalidTag($res['name']);
                    $e->setHtmlLine($res['line']);
                    throw $e;
                } else {
                    array_pop($parents);
                }
            } else {
                // if it is an auto-closed tag
                if ($res['autoclose']) {
                    // save the opened tag
                    $actions[] = $res;

                    // prepare the closed tag
                    $res['params'] = array();
                    $res['close'] = true;
                } else {
                    // else: add a child for validation
                    array_push($parents, $res['name']);
                }
            }

            // if it is a <pre> tag (or <code> tag) not auto-closed => update the flag
            if (($res['name'] == 'pre' || $res['name'] == 'code') && !$res['autoclose']) {
                $this->tagPreIn = !$res['close'];
            }
        }

        // save the actions to convert
        $actions[] = $res;

        return $actions;
    }

    protected function getTextAction(Token $token)
    {
        // action to use for each line of the content of a <pre> Tag
        $tagPreBr = array(
            'name' => 'br',
            'close' => false,
            'param' => array(
                'style' => array(),
                'num'    => 0
            )
        );
        $actions = array();

        // if we are not in a <pre> tag
        if (!$this->tagPreIn) {
            // save the action
            $actions[] = array(
                'name'  => 'write',
                'close' => false,
                'param' => array('txt' => $this->textParser->prepareTxt($token->getData())),
            );
        } else { // else (if we are in a <pre> tag)
            // prepare the text
            $data = str_replace("\r", '', $token->getData());
            $lines = explode("\n", $data);

            // foreach line of the text
            foreach ($lines as $k => $txt) {
                // transform the line
                $txt = str_replace("\t", self::HTML_TAB, $txt);
                $txt = str_replace(' ', '&nbsp;', $txt);

                // add a break line
                if ($k > 0) {
                    $actions[] = $tagPreBr;
                }

                // save the action
                $actions[] = array(
                    'name'  => 'write',
                    'close' => false,
                    'param' => array('txt' => $this->textParser->prepareTxt($txt, false)),
                );
            }
        }
        return $actions;
    }

    /**
     * get a full level of HTML, between an opening and closing corresponding
     *
     * @param   integer $k
     * @return  array   actions
     */
    public function getLevel($k)
    {
        // if the code does not exist => return empty
        if (!isset($this->code[$k])) {
            return array();
        }

        // the tag to detect
        $detect = $this->code[$k]['name'];

        // if it is a text => return
        if ($detect == 'write') {
            return array($this->code[$k]);
        }

        //
        $level = 0;      // depth level
        $end = false;    // end of the search
        $code = array(); // extract code

        // while it's not ended
        while (!$end) {
            // current action
            $row = $this->code[$k];

            // if 'write' => we add the text
            if ($row['name']=='write') {
                $code[] = $row;
            } else { // else, it is a html tag
                $not = false; // flag for not taking into account the current tag

                // if it is the searched tag
                if ($row['name'] == $detect) {
                    // if we are just at the root level => dont take it
                    if ($level == 0) {
                        $not = true;
                    }

                    // update the level
                    $level+= ($row['close'] ? -1 : 1);

                    // if we are now at the root level => it is the end, and dont take it
                    if ($level == 0) {
                        $not = true;
                        $end = true;
                    }
                }

                // if we can take into account the current tag => save it
                if (!$not) {
                    if (isset($row['style']['text-align'])) {
                        unset($row['style']['text-align']);
                    }
                    $code[] = $row;
                }
            }

            // it continues as long as there has code to analyze
            if (isset($this->code[$k + 1])) {
                $k++;
            } else {
                $end = true;
            }
        }

        // return the extract
        return $code;
    }
}
