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
     * @var TagParser
     */
    protected $tagParser;

    /**
     * @var TextParser
     */
    protected $textParser;

    /**
     * are we in a pre ?
     * @var boolean
     */
    protected $tagPreIn = false;

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
     * @param Token[] $tokens A list of tokens to parse
     *
     * @throws HtmlParsingException
     */
    public function parse($tokens)
    {
        $parents = array();

        // flag : are we in a <pre> Tag ?
        $this->tagPreIn = false;

        /**
         * all the actions to do
         * @var Node[] $actions
         */
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
            if ($actions[$k]->getName() =='write') {
                // if the tag before the text is a tag to clean => ltrim on the text
                if ($k>0 && in_array($actions[$k - 1]->getName(), $tagsToClean)) {
                    $actions[$k]->setParam('txt', ltrim($actions[$k]->getParam('txt')));
                }

                // if the tag after the text is a tag to clean => rtrim on the text
                if ($k < $nb - 1 && in_array($actions[$k + 1]->getName(), $tagsToClean)) {
                    $actions[$k]->setParam('txt', rtrim($actions[$k]->getParam('txt')));
                }

                // if the text is empty => remove the action
                if (!strlen($actions[$k]->getParam('txt'))) {
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
        $node = $this->tagParser->analyzeTag($token->getData());

        // save the current position in the HTML code
        $node->setLine($token->getLine());

        $actions = array();
        // if the tag must be closed
        if (!in_array($node->getName(), $tagsNotClosed)) {
            // if it is a closure tag
            if ($node->isClose()) {
                // HTML validation
                if (count($parents) < 1) {
                    $e = new HtmlParsingException('Too many tag closures found for ['.$node->getName().']');
                    $e->setInvalidTag($node->getName());
                    $e->setHtmlLine($token->getLine());
                    throw $e;
                } elseif (end($parents) != $node->getName()) {
                    $e = new HtmlParsingException('Tags are closed in a wrong order for ['.$node->getName().']');
                    $e->setInvalidTag($node->getName());
                    $e->setHtmlLine($token->getLine());
                    throw $e;
                } else {
                    array_pop($parents);
                }
            } else {
                // if it is an auto-closed tag
                if ($node->isAutoClose()) {
                    // save the opened tag
                    $actions[] = $node;

                    // prepare the closed tag
                    $node = clone $node;
                    $node->setParams(array());
                    $node->setClose(true);
                } else {
                    // else: add a child for validation
                    array_push($parents, $node->getName());
                }
            }

            // if it is a <pre> tag (or <code> tag) not auto-closed => update the flag
            if (($node->getName() == 'pre' || $node->getName() == 'code') && !$node->isAutoClose()) {
                $this->tagPreIn = !$node->isClose();
            }
        }

        // save the actions to convert
        $actions[] = $node;

        return $actions;
    }

    /**
     * get the Text action
     *
     * @param Token $token
     *
     * @return array
     */
    protected function getTextAction(Token $token)
    {
        // action to use for each line of the content of a <pre> Tag
        $tagPreBr = new Node('br', array('style' => array(), 'num' => 0), false);

        $actions = array();

        // if we are not in a <pre> tag
        if (!$this->tagPreIn) {
            // save the action
            $actions[] = new Node('write', array('txt' => $this->textParser->prepareTxt($token->getData())), false);
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
                    $actions[] = clone $tagPreBr;
                }

                // save the action
                $actions[] = new Node('write', array('txt' => $this->textParser->prepareTxt($txt, false)), false);
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
