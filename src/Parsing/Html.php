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
namespace Spipu\Html2Pdf\Parsing;

use Spipu\Html2Pdf\Exception\HtmlParsingException;

/**
 * Class Html
 */
class Html
{
    const HTML_TAB = '        ';

    /**
     * @var TokenStream
     */
    protected $tokenStream;

    /**
     * @var Node
     */
    public $root;

    /**
     * @var TagParser
     */
    protected $tagParser;

    /**
     * @var TextParser
     */
    protected $textParser;

    /**
     * parsed HTML code
     * @var Node[]
     */
    public $code = array();

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
     * @param TokenStream $tokens A list of tokens to parse
     *
     * @throws HtmlParsingException
     */
    public function parse(TokenStream $tokens)
    {
        $this->tokenStream = $tokens;

        /**
         * all the actions to do
         * @var Node[] $actions
         */
        $actions = array();

        $rootNode = $this->parseLevel();

        // save the actions to do
        $this->code = array_values($actions);
        $this->root = $rootNode;
    }

    /**
     * @param Token $tokenOpen
     *
     * @return Node
     * @throws HtmlParsingException
     * @throws \Exception
     */
    protected function parseLevel($tokenOpen = null)
    {
        $tagsNotClosed = array(
            'br', 'hr', 'img', 'col',
            'input', 'link', // TODO option was removed, should we keep it ?
            'circle', 'ellipse', 'path', 'rect', 'line', 'polygon', 'polyline'
        );

        if ($tokenOpen) {
            list($nodeName, $nodeParam) = $this->tagParser->analyzeTag($tokenOpen->getData());
            if ($tokenOpen->getType() == Token::TAG_AUTOCLOSE_TYPE || in_array($nodeName, $tagsNotClosed)) {
                return new Node($nodeName, $nodeParam);
            }
        } else {
            $nodeParam = array();
            $nodeName = 'root';
        }

        $closed = $tokenOpen === null;
        $nodes = array();
        while (($token = $this->tokenStream->current()) !== null) {
            $this->tokenStream->next();
            if ($token->getType() == Token::TAG_OPEN_TYPE) {
                // if tag open -> children to parse again
                $nodes[] = $this->parseLevel($token);
            } elseif ($token->getType() == Token::TAG_CLOSE_TYPE) {
                list($name, $param) = $this->tagParser->analyzeTag($token->getData());
                if ($tokenOpen && $name == $nodeName) { // if next token is close tag for $token, we got the children, exit
                    $closed = true;
                    break;
                } else { // closing tag not matching
                    throw new HtmlParsingException('Unexpected closing tag:'. $name . ' expected '. $nodeName);
                }
            } elseif ($token->getType() == Token::TAG_AUTOCLOSE_TYPE) {
                $nodes[] = $this->parseLevel($token);
            } elseif ($token->getType() == Token::TEXT_TYPE) {
                if ($nodeName == 'pre') {
                    $nodes = array_merge($nodes, $this->preparePreChildren($token->getData()));
                } else {
                    $text = $token->getData();
                    if (empty($nodes)) {
                        $previousNodeName = $nodeName;
                    } else {
                        $previousNode = end($nodes);
                        $previousNodeName = $previousNode->getName();
                    }
                    $text = $this->cleanWhiteSpace($text, $previousNodeName);

                    if ($text == '') {
                        continue;
                    }
                    $nodes[] = new Node('write', array('txt' => $this->textParser->prepareTxt($text)));
                }
            } else {
                throw new HtmlParsingException('Unknown token type '.$token->getType());
            }
        }

        if (!$closed) {
            $errorMsg = 'The following tag has not been closed: ';
            $e = new HtmlParsingException($errorMsg. $nodeName);
            $e->setInvalidTag($nodeName);
            throw $e;
        }

        return new Node($nodeName, $nodeParam, $nodes);
    }

    /**
     * @param string $text
     * @param string $previousNodeName
     *
     * @return string
     */
    protected function cleanWhiteSpace($text, $previousNodeName)
    {
        $tagsToCleanSpaces = array(
            'root',
            'page', 'page_header', 'page_footer', 'form',
            'table', 'thead', 'tfoot', 'tr', 'td', 'th', 'br',
            'div', 'hr', 'p', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'bookmark', 'fieldset', 'legend',
            'draw', 'circle', 'ellipse', 'path', 'rect', 'line', 'g', 'polygon', 'polyline',
            'option'
        );

        $nextToken = $this->tokenStream->current();
        if ($nextToken === null) {
            return rtrim($text);
        }

        if ($previousNodeName !== 'write') {
            if (in_array($previousNodeName, $tagsToCleanSpaces)) { // parent is a block to clean
                $text = ltrim($text);
            }
        }
        if ($nextToken->getType() !== Token::TEXT_TYPE) {
            list($nextNodeName, $param) = $this->tagParser->analyzeTag($nextToken->getData());
            if (in_array($nextNodeName, $tagsToCleanSpaces)) { // previous sibling (closed) is to clean
                $text = ltrim($text);
            }
        }

        return $text;
    }

    /**
     * Prepare the text contained in a <pre> tag for formatting purposes
     *
     * @param string $text
     *
     * @return array
     */
    protected function preparePreChildren($text)
    {
        $children = array();
        $tagPreBr = new Node('br', array('style' => array(), 'num' => 0));

        // prepare the text
        $data = str_replace("\r", '', $text);
        $lines = explode("\n", $data);

        // foreach line of the text
        foreach ($lines as $k => $txt) {
            // transform the line
            $txt = str_replace("\t", self::HTML_TAB, $txt);
            $txt = str_replace(' ', '&nbsp;', $txt);

            // add a break line
            if ($k > 0) {
                $children[] = clone $tagPreBr;
            }

            // save the action
            $children[] = new Node('write', array('txt' => $this->textParser->prepareTxt($txt, false)));
        }

        return $children;
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
        $detect = $this->code[$k]->getName();

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
            /** @var Node $node */
            $node = $this->code[$k];

            // if 'write' => we add the text
            if ($node->getName() == 'write') {
                $code[] = $node;
            } else { // else, it is a html tag
                $not = false; // flag for not taking into account the current tag

                // if it is the searched tag
                if ($node->getName() == $detect) {
                    // if we are just at the root level => dont take it
                    if ($level == 0) {
                        $not = true;
                    }

                    // update the level
                    $level += ($node->isClose() ? -1 : 1);

                    // if we are now at the root level => it is the end, and dont take it
                    if ($level == 0) {
                        $not = true;
                        $end = true;
                    }
                }

                // if we can take into account the current tag => save it
                if (!$not) {
                    $code[] = $node;
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

    /**
     * prepare the HTML
     *
     * @param string $html
     *
     * @return string
     */
    public function prepareHtml($html)
    {
        // if it is a real html page, we have to convert it
        if (preg_match('/<body/isU', $html)) {
            $html = $this->getHtmlFromRealPage($html);
        }

        // replace some constants
        $html = str_replace('[[date_y]]', date('Y'), $html);
        $html = str_replace('[[date_m]]', date('m'), $html);
        $html = str_replace('[[date_d]]', date('d'), $html);

        $html = str_replace('[[date_h]]', date('H'), $html);
        $html = str_replace('[[date_i]]', date('i'), $html);
        $html = str_replace('[[date_s]]', date('s'), $html);

        return $html;
    }

    /**
     * convert the HTML of a real page, to a code adapted to Html2Pdf
     *
     * @param  string $html HTML code of a real page
     * @return string HTML adapted to Html2Pdf
     */
    protected function getHtmlFromRealPage($html)
    {
        // set body tag to lower case
        $html = str_replace('<BODY', '<body', $html);
        $html = str_replace('</BODY', '</body', $html);

        // explode from the body tag. If no body tag => end
        $res = explode('<body', $html);
        if (count($res)<2) {
            return $html;
        }

        // the html content is between body tag openning and closing
        $content = '<page'.$res[1];
        $content = explode('</body', $content);
        $content = $content[0].'</page>';

        // extract the link tags from the original html
        // and add them before the content
        preg_match_all('/<link([^>]*)>/isU', $html, $match);
        foreach ($match[0] as $src) {
            $content = $src.'</link>'.$content;
        }

        // extract the css style tags from the original html
        // and add them before the content
        preg_match_all('/<style[^>]*>(.*)<\/style[^>]*>/isU', $html, $match);
        foreach ($match[0] as $src) {
            $content = $src.$content;
        }

        return $content;
    }
}
