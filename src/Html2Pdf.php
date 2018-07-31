<?php
/**
 * Html2Pdf Library - main class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf;

use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ImageException;
use Spipu\Html2Pdf\Exception\LongSentenceException;
use Spipu\Html2Pdf\Exception\TableException;
use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\Extension\Core;
use Spipu\Html2Pdf\Extension\ExtensionInterface;
use Spipu\Html2Pdf\Parsing\HtmlLexer;
use Spipu\Html2Pdf\Parsing\Node;
use Spipu\Html2Pdf\Parsing\TagParser;
use Spipu\Html2Pdf\Parsing\TextParser;
use Spipu\Html2Pdf\Tag\TagInterface;
use Spipu\Html2Pdf\Debug\DebugInterface;
use Spipu\Html2Pdf\Debug\Debug;

require_once dirname(__FILE__) . '/config/tcpdf.config.php';

class Html2Pdf
{
    /**
     * myPdf object, extends from TCPDF
     * @var MyPdf
     */
    public $pdf = null;

    /**
     * CSS parsing
     * @var Parsing\Css
     */
    public $parsingCss = null;

    /**
     * HTML parsing
     * @var Parsing\Html
     */
    public $parsingHtml = null;

    /**
     * @var Debug
     */
    private $debug;

    /**
     * @var HtmlLexer
     */
    private $lexer;

    /**
     * @var CssConverter
     */
    private $cssConverter;

    /**
     * @var SvgDrawer
     */
    private $svgDrawer;

    protected $_langue           = 'fr';        // locale of the messages
    protected $_orientation      = 'P';         // page orientation : Portrait ou Landscape
    protected $_format           = 'A4';        // page format : A4, A3, ...
    protected $_encoding         = '';          // charset encoding
    protected $_unicode          = true;        // means that the input text is unicode (default = true)

    protected $_testTdInOnepage  = true;        // test of TD that can not take more than one page
    protected $_testIsImage      = true;        // test if the images exist or not
    protected $_fallbackImage    = null;        // fallback image to use in img tags

    protected $_parsePos         = 0;           // position in the parsing
    protected $_tempPos          = 0;           // temporary position for complex table
    protected $_page             = 0;           // current page number

    protected $_subHtml          = null;        // sub html
    protected $_subPart          = false;       // sub Html2Pdf
    protected $_subHEADER        = array();     // sub action to make the header
    protected $_subFOOTER        = array();     // sub action to make the footer
    protected $_subSTATES        = array();     // array to save some parameters

    protected $_isSubPart        = false;       // flag : in a sub html2pdf
    protected $_isInThead        = false;       // flag : in a thead
    protected $_isInTfoot        = false;       // flag : in a tfoot
    protected $_isInOverflow     = false;       // flag : in a overflow
    protected $_isInFooter       = false;       // flag : in a footer
    protected $_isInDraw         = null;        // flag : in a draw (svg)
    protected $_isAfterFloat     = false;       // flag : is just after a float
    protected $_isInForm         = false;       // flag : is in a float. false / action of the form
    protected $_isInLink         = '';          // flag : is in a link. empty / href of the link
    protected $_isInParagraph    = false;       // flag : is in a paragraph
    protected $_isForOneLine     = false;       // flag : in a specific sub html2pdf to have the height of the next line

    protected $_maxX             = 0;           // maximum X of the current zone
    protected $_maxY             = 0;           // maximum Y of the current zone
    protected $_maxE             = 0;           // number of elements in the current zone
    protected $_maxH             = 0;           // maximum height of the line in the current zone
    protected $_maxSave          = array();     // save the maximums of the current zone
    protected $_currentH         = 0;           // height of the current line

    protected $_defaultLeft      = 0;           // default marges of the page
    protected $_defaultTop       = 0;
    protected $_defaultRight     = 0;
    protected $_defaultBottom    = 0;
    protected $_defaultFont      = null;        // default font to use, is the asked font does not exist

    protected $_margeLeft        = 0;           // current marges of the page
    protected $_margeTop         = 0;
    protected $_margeRight       = 0;
    protected $_margeBottom      = 0;
    protected $_marges           = array();     // save the different marges of the current page
    protected $_pageMarges       = array();     // float marges of the current page
    protected $_background       = array();     // background informations

    protected $_hideHeader       = array();     // array : list of pages which the header gonna be hidden
    protected $_hideFooter       = array();     // array : list of pages which the footer gonna be hidden
    protected $_firstPage        = true;        // flag : first page
    protected $_defList          = array();     // table to save the stats of the tags UL and OL

    protected $_lstAnchor        = array();     // list of the anchors
    protected $_lstField         = array();     // list of the fields
    protected $_lstSelect        = array();     // list of the options of the current select
    protected $_previousCall     = null;        // last action called

    protected $_sentenceMaxLines = 1000;        // max number of lines for a sentence

    /**
     * @var Html2Pdf
     */
    static protected $_subobj    = null;        // object html2pdf prepared in order to accelerate the creation of sub html2pdf
    static protected $_tables    = array();     // static table to prepare the nested html tables

    /**
     * list of tag definitions
     * @var ExtensionInterface[]
     */
    protected $extensions = array();

    /**
     * List of tag objects
     * @var TagInterface[]
     */
    protected $tagObjects = array();

    /**
     * @var bool
     */
    protected $extensionsLoaded = false;

    /**
     * class constructor
     *
     * @param string  $orientation page orientation, same as TCPDF
     * @param mixed   $format      The format used for pages, same as TCPDF
     * @param string  $lang        Lang : fr, en, it...
     * @param boolean $unicode     TRUE means that the input text is unicode (default = true)
     * @param String  $encoding    charset encoding; default is UTF-8
     * @param array   $margins     Default margins (left, top, right, bottom)
     * @param boolean $pdfa        If TRUE set the document to PDF/A mode.
     *
     * @return Html2Pdf
     */
    public function __construct(
        $orientation = 'P',
        $format = 'A4',
        $lang = 'fr',
        $unicode = true,
        $encoding = 'UTF-8',
        $margins = array(5, 5, 5, 8),
        $pdfa = false
    ) {
        // init the page number
        $this->_page         = 0;
        $this->_firstPage    = true;

        // save the parameters
        $this->_orientation  = $orientation;
        $this->_format       = $format;
        $this->_langue       = strtolower($lang);
        $this->_unicode      = $unicode;
        $this->_encoding     = $encoding;
        $this->_pdfa         = $pdfa;

        // load the Locale
        Locale::load($this->_langue);

        // create the  myPdf object
        $this->pdf = new MyPdf($orientation, 'mm', $format, $unicode, $encoding, false, $pdfa);

        // init the CSS parsing object
        $this->cssConverter = new CssConverter();
        $textParser = new TextParser($encoding);
        $this->parsingCss = new Parsing\Css($this->pdf, new TagParser($textParser), $this->cssConverter);
        $this->parsingCss->fontSet();
        $this->_defList = array();

        // init some tests
        $this->setTestTdInOnePage(true);
        $this->setTestIsImage(true);

        // init the default font
        $this->setDefaultFont(null);

        $this->lexer = new HtmlLexer();
        // init the HTML parsing object
        $this->parsingHtml = new Parsing\Html($textParser);
        $this->_subHtml = null;
        $this->_subPart = false;

        $this->setDefaultMargins($margins);
        $this->setMargins();
        $this->_marges = array();

        // init the form's fields
        $this->_lstField = array();

        $this->svgDrawer = new SvgDrawer($this->pdf, $this->cssConverter);

        $this->addExtension(new Core\HtmlExtension());
        $this->addExtension(new Core\SvgExtension($this->svgDrawer));

        return $this;
    }

    /**
     * Gets the detailed version as array
     *
     * @return array
     */
    public function getVersionAsArray()
    {
        return array(
            'major'     => 5,
            'minor'     => 2,
            'revision'  => 1
        );
    }

    /**
     * Gets the current version as string
     *
     * @return string
     */
    public function getVersion()
    {
        $v = $this->getVersionAsArray();
        return $v['major'].'.'.$v['minor'].'.'.$v['revision'];
    }

    /**
     * Clone to create a sub Html2Pdf from self::$_subobj
     *
     * @access public
     */
    public function __clone()
    {
        $this->pdf = clone $this->pdf;
        $this->parsingHtml = clone $this->parsingHtml;
        $this->parsingCss = clone $this->parsingCss;
        $this->parsingCss->setPdfParent($this->pdf);
    }

    /**
     * Set the max number of lines for a sentence
     *
     * @param int $nbLines
     *
     * @return $this
     */
    public function setSentenceMaxLines($nbLines)
    {
        $this->_sentenceMaxLines = (int) $nbLines;

        return $this;
    }

    /**
     * Get the max number of lines for a sentence
     *
     * @return int
     */
    public function getSentenceMaxLines()
    {
        return $this->_sentenceMaxLines;
    }

    /**
     * @param ExtensionInterface $extension
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $name = strtolower($extension->getName());
        $this->extensions[$name] = $extension;
    }

    /**
     * Get the number of pages
     * @return int
     */
    public function getNbPages()
    {
        return $this->_page;
    }

    /**
     * Initialize the registered extensions
     *
     * @throws Html2PdfException
     */
    protected function loadExtensions()
    {
        if ($this->extensionsLoaded) {
            return;
        }
        foreach ($this->extensions as $extension) {
            foreach ($extension->getTags() as $tag) {
                if (!$tag instanceof TagInterface) {
                    throw new Html2PdfException('The ExtensionInterface::getTags() method must return an array of TagInterface.');
                }
                $this->addTagObject($tag);
            }
        }

        $this->extensionsLoaded = true;
    }

    /**
     * register a tag object
     *
     * @param TagInterface $tagObject the object
     */
    protected function addTagObject(TagInterface $tagObject)
    {
        $tagName = strtolower($tagObject->getName());
        $this->tagObjects[$tagName] = $tagObject;
    }

    /**
     * get the tag object from a tag name
     *
     * @param string $tagName tag name to load
     *
     * @return TagInterface|null
     */
    protected function getTagObject($tagName)
    {
        if (!$this->extensionsLoaded) {
            $this->loadExtensions();
        }

        if (!array_key_exists($tagName, $this->tagObjects)) {
            return null;
        }

        $tagObject = $this->tagObjects[$tagName];
        $tagObject->setParsingCssObject($this->parsingCss);
        $tagObject->setCssConverterObject($this->cssConverter);
        $tagObject->setPdfObject($this->pdf);
        if (!is_null($this->debug)) {
            $tagObject->setDebugObject($this->debug);
        }

        return $tagObject;
    }

    /**
     * set the debug mode to On
     *
     * @param DebugInterface $debugObject
     *
     * @return Html2Pdf $this
     */
    public function setModeDebug(DebugInterface $debugObject = null)
    {
        if (is_null($debugObject)) {
            $this->debug = new Debug();
        } else {
            $this->debug = $debugObject;
        }
        $this->debug->start();

        return $this;
    }

    /**
     * Set the test of TD that can not take more than one page
     *
     * @access public
     * @param  boolean  $mode
     * @return Html2Pdf $this
     */
    public function setTestTdInOnePage($mode = true)
    {
        $this->_testTdInOnepage = $mode ? true : false;

        return $this;
    }

    /**
     * Set the test if the images exist or not
     *
     * @access public
     * @param  boolean  $mode
     * @return Html2Pdf $this
     */
    public function setTestIsImage($mode = true)
    {
        $this->_testIsImage = $mode ? true : false;

        return $this;
    }

    /**
     * Set the default font to use, if no font is specified, or if the asked font does not exist
     *
     * @access public
     * @param  string   $default name of the default font to use. If null : Arial if no font is specified, and error if the asked font does not exist
     * @return Html2Pdf $this
     */
    public function setDefaultFont($default = null)
    {
        $this->_defaultFont = $default;
        $this->parsingCss->setDefaultFont($default);

        return $this;
    }

    /**
     * Set a fallback image
     *
     * @param string $fallback Path or URL to the fallback image
     *
     * @return $this
     */
    public function setFallbackImage($fallback)
    {
        $this->_fallbackImage = $fallback;

        return $this;
    }

    /**
     * add a font, see TCPDF function addFont
     *
     * @access public
     * @param string $family Font family. The name can be chosen arbitrarily. If it is a standard family name, it will override the corresponding font.
     * @param string $style Font style. Possible values are (case insensitive):<ul><li>empty string: regular (default)</li><li>B: bold</li><li>I: italic</li><li>BI or IB: bold italic</li></ul>
     * @param string $file The font definition file. By default, the name is built from the family and style, in lower case with no spaces.
     * @return Html2Pdf $this
     * @see TCPDF::addFont
     */
    public function addFont($family, $style = '', $file = '')
    {
        $this->pdf->AddFont($family, $style, $file);

        return $this;
    }

    /**
     * display a automatic index, from the bookmarks
     *
     * @access public
     * @param  string  $titre         index title
     * @param  int     $sizeTitle     font size of the index title, in mm
     * @param  int     $sizeBookmark  font size of the index, in mm
     * @param  boolean $bookmarkTitle add a bookmark for the index, at his beginning
     * @param  boolean $displayPage   display the page numbers
     * @param  int     $onPage        if null : at the end of the document on a new page, else on the $onPage page
     * @param  string  $fontName      font name to use
     * @param  string  $marginTop     margin top to use on the index page
     * @return null
     */
    public function createIndex(
        $titre = 'Index',
        $sizeTitle = 20,
        $sizeBookmark = 15,
        $bookmarkTitle = true,
        $displayPage = true,
        $onPage = null,
        $fontName = null,
        $marginTop = null
    ) {
        if ($fontName === null) {
            $fontName = 'helvetica';
        }

        $oldPage = $this->_INDEX_NewPage($onPage);

        if ($marginTop !== null) {
            $marginTop = $this->cssConverter->convertToMM($marginTop);
            $this->pdf->SetY($this->pdf->GetY() + $marginTop);
        }

        $this->pdf->createIndex($this, $titre, $sizeTitle, $sizeBookmark, $bookmarkTitle, $displayPage, $onPage, $fontName);
        if ($oldPage) {
            $this->pdf->setPage($oldPage);
        }
    }

    /**
     * clean up the objects, if the method output can not be called because of an exception
     *
     * @return Html2Pdf
     */
    public function clean()
    {
        self::$_subobj = null;
        self::$_tables = array();

        Locale::clean();

        return $this;
    }

    /**
     * Send the document to a given destination: string, local file or browser.
     * Dest can be :
     *  I : send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
     *  D : send to the browser and force a file download with the name given by name.
     *  F : save to a local server file with the name given by name.
     *  S : return the document as a string (name is ignored).
     *  FI: equivalent to F + I option
     *  FD: equivalent to F + D option
     *  E : return the document as base64 mime multi-part email attachment (RFC 2045)
     *
     * @param string $name The name of the file when saved.
     * @param string $dest Destination where to send the document.
     *
     * @throws Html2PdfException
     * @return string content of the PDF, if $dest=S
     * @see    TCPDF::close
     */
    public function output($name = 'document.pdf', $dest = 'I')
    {
        // if on debug mode
        if (!is_null($this->debug)) {
            $this->debug->stop();
            $this->pdf->Close();
            return '';
        }

        //Normalize parameters
        $dest = strtoupper($dest);
        if (!in_array($dest, array('I', 'D', 'F', 'S', 'FI','FD', 'E'))) {
            throw new Html2PdfException('The output destination mode ['.$dest.'] is invalid');
        }

        if ($dest !== 'S') {
            // the name must be a PDF name
            if (strtolower(substr($name, -4)) !== '.pdf') {
                throw new Html2PdfException('The output document name [' . $name . '] is not a PDF name');
            }
        }

        // if save on server: it must be an absolute path
        if ($dest[0] === 'F') {
            $isWindowsPath = preg_match("/^[A-Z]:\\\\/", $name);
            // If windows is not saving on a remote file server
            if($name[0] !== DIRECTORY_SEPARATOR &&  $isWindowsPath === false ){
                $name = getcwd() . DIRECTORY_SEPARATOR . $name;
            }
        }

        // call the output of TCPDF
        $output = $this->pdf->Output($name, $dest);
        
        // close the pdf and clean up
        $this->clean();

        return $output;
    }

    /**
     * convert HTML to PDF
     *
     * @param string $html
     *
     * @return Html2Pdf
     */
    public function writeHTML($html)
    {
        $html = $this->parsingHtml->prepareHtml($html);
        $html = $this->parsingCss->extractStyle($html);
        $this->parsingHtml->parse($this->lexer->tokenize($html));
        $this->_makeHTMLcode();

        return $this;
    }


    /**
     * Preview the HTML before conversion
     *
     * @param string $html
     *
     * @return void
     */
    public function previewHTML($html)
    {
        $html = $this->parsingHtml->prepareHtml($html);

        $html = preg_replace('/<page([^>]*)>/isU', '<hr>Page : $1<hr><div$1>', $html);
        $html = preg_replace('/<page_header([^>]*)>/isU', '<hr>Page Header : $1<hr><div$1>', $html);
        $html = preg_replace('/<page_footer([^>]*)>/isU', '<hr>Page Footer : $1<hr><div$1>', $html);
        $html = preg_replace('/<\/page([^>]*)>/isU', '</div><hr>', $html);
        $html = preg_replace('/<\/page_header([^>]*)>/isU', '</div><hr>', $html);
        $html = preg_replace('/<\/page_footer([^>]*)>/isU', '</div><hr>', $html);

        $html = preg_replace('/<bookmark([^>]*)>/isU', '<hr>bookmark : $1<hr>', $html);
        $html = preg_replace('/<\/bookmark([^>]*)>/isU', '', $html);

        $html = preg_replace('/<barcode([^>]*)>/isU', '<hr>barcode : $1<hr>', $html);
        $html = preg_replace('/<\/barcode([^>]*)>/isU', '', $html);

        $html = preg_replace('/<qrcode([^>]*)>/isU', '<hr>qrcode : $1<hr>', $html);
        $html = preg_replace('/<\/qrcode([^>]*)>/isU', '', $html);

        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>HTML View</title>
        <meta http-equiv="Content-Type" content="text/html; charset='.$this->_encoding.'" >
    </head>
    <body style="padding: 10px; font-size: 10pt;font-family:    Verdana;">
    '.$html.'
    </body>
</html>';
    }

    /**
     * init a sub Html2Pdf. do not use it directly. Only the method createSubHTML must use it
     *
     * @access public
     * @param  string  $format
     * @param  string  $orientation
     * @param  array   $marge
     * @param  integer $page
     * @param  array   $defLIST
     * @param  integer $myLastPageGroup
     * @param  integer $myLastPageGroupNb
     */
    public function initSubHtml($format, $orientation, $marge, $page, $defLIST, $myLastPageGroup, $myLastPageGroupNb)
    {
        $this->_isSubPart = true;

        $this->parsingCss->setOnlyLeft();

        $this->_setNewPage($format, $orientation, null, null, ($myLastPageGroup !== null));

        $this->_saveMargin(0, 0, $marge);
        $this->_defList = $defLIST;

        $this->_page = $page;
        $this->pdf->setMyLastPageGroup($myLastPageGroup);
        $this->pdf->setMyLastPageGroupNb($myLastPageGroupNb);
        $this->pdf->SetXY(0, 0);
        $this->parsingCss->fontSet();
    }

    /**
     * set the default margins of the page
     *
     * @param array|int $margins (mm, left top right bottom)
     */
    protected function setDefaultMargins($margins)
    {
        if (!is_array($margins)) {
            $margins = array($margins, $margins, $margins, $margins);
        }

        if (!isset($margins[2])) {
            $margins[2] = $margins[0];
        }
        if (!isset($margins[3])) {
            $margins[3] = 8;
        }

        $this->_defaultLeft   = $this->cssConverter->convertToMM($margins[0].'mm');
        $this->_defaultTop    = $this->cssConverter->convertToMM($margins[1].'mm');
        $this->_defaultRight  = $this->cssConverter->convertToMM($margins[2].'mm');
        $this->_defaultBottom = $this->cssConverter->convertToMM($margins[3].'mm');
    }

    /**
     * create a new page
     *
     * @access protected
     * @param  mixed   $format
     * @param  string  $orientation
     * @param  array   $background background information
     * @param  integer $curr real position in the html parser (if break line in the write of a text)
     * @param  boolean $resetPageNumber
     */
    protected function _setNewPage($format = null, $orientation = '', $background = null, $curr = null, $resetPageNumber = false)
    {
        $this->_firstPage = false;

        $this->_format = $format ? $format : $this->_format;
        $this->_orientation = $orientation ? $orientation : $this->_orientation;
        $this->_background = $background !== null ? $background : $this->_background;
        $this->_maxY = 0;
        $this->_maxX = 0;
        $this->_maxH = 0;
        $this->_maxE = 0;

        $this->pdf->SetMargins($this->_defaultLeft, $this->_defaultTop, $this->_defaultRight);

        if ($resetPageNumber) {
            $this->pdf->startPageGroup();
        }

        $this->pdf->AddPage($this->_orientation, $this->_format);

        if ($resetPageNumber) {
            $this->pdf->myStartPageGroup();
        }

        $this->_page++;

        if (!$this->_subPart && !$this->_isSubPart) {
            if (is_array($this->_background)) {
                if (isset($this->_background['color']) && $this->_background['color']) {
                    $this->pdf->SetFillColorArray($this->_background['color']);
                    $this->pdf->Rect(0, 0, $this->pdf->getW(), $this->pdf->getH(), 'F');
                }

                if (isset($this->_background['img']) && $this->_background['img']) {
                    $this->pdf->Image($this->_background['img'], $this->_background['posX'], $this->_background['posY'], $this->_background['width']);
                }
            }

            $this->_setPageHeader();
            $this->_setPageFooter();
        }

        $this->setMargins();
        $this->pdf->SetY($this->_margeTop);

        $this->_setNewPositionForNewLine($curr);
        $this->_maxH = 0;
    }

    /**
     * set the real margin, using the default margins and the page margins
     */
    protected function setMargins()
    {
        // prepare the margins
        $this->_margeLeft   = $this->_defaultLeft   + (isset($this->_background['left'])   ? $this->_background['left']   : 0);
        $this->_margeRight  = $this->_defaultRight  + (isset($this->_background['right'])  ? $this->_background['right']  : 0);
        $this->_margeTop    = $this->_defaultTop    + (isset($this->_background['top'])    ? $this->_background['top']    : 0);
        $this->_margeBottom = $this->_defaultBottom + (isset($this->_background['bottom']) ? $this->_background['bottom'] : 0);

        // set the PDF margins
        $this->pdf->SetMargins($this->_margeLeft, $this->_margeTop, $this->_margeRight);
        $this->pdf->SetAutoPageBreak(false, $this->_margeBottom);

        // set the float Margins
        $this->_pageMarges = array();
        if ($this->_isInParagraph !== false) {
            $this->_pageMarges[floor($this->_margeTop*100)] = array($this->_isInParagraph[0], $this->pdf->getW()-$this->_isInParagraph[1]);
        } else {
            $this->_pageMarges[floor($this->_margeTop*100)] = array($this->_margeLeft, $this->pdf->getW()-$this->_margeRight);
        }
    }


    /**
     * get the Min and Max X, for Y (use the float margins)
     *
     * @access protected
     * @param  float $y
     * @return array(float, float)
     */
    protected function _getMargins($y)
    {
        $y = floor($y*100);
        $x = array($this->pdf->getlMargin(), $this->pdf->getW()-$this->pdf->getrMargin());

        foreach ($this->_pageMarges as $mY => $mX) {
            if ($mY<=$y) {
                $x = $mX;
            }
        }

        return $x;
    }

    /**
     * Add margins, for a float
     *
     * @access protected
     * @param  string $float (left / right)
     * @param  float  $xLeft
     * @param  float  $yTop
     * @param  float  $xRight
     * @param  float  $yBottom
     */
    protected function _addMargins($float, $xLeft, $yTop, $xRight, $yBottom)
    {
        // get the current float margins, for top and bottom
        $oldTop    = $this->_getMargins($yTop);
        $oldBottom = $this->_getMargins($yBottom);

        // update the top float margin
        if ($float === 'left'  && $oldTop[0]<$xRight) {
            $oldTop[0] = $xRight;
        }
        if ($float === 'right' && $oldTop[1]>$xLeft) {
            $oldTop[1] = $xLeft;
        }

        $yTop = floor($yTop*100);
        $yBottom = floor($yBottom*100);

        // erase all the float margins that are smaller than the new one
        foreach ($this->_pageMarges as $mY => $mX) {
            if ($mY<$yTop) {
                continue;
            }
            if ($mY>$yBottom) {
                break;
            }
            if ($float === 'left' && $this->_pageMarges[$mY][0]<$xRight) {
                unset($this->_pageMarges[$mY]);
            }
            if ($float === 'right' && $this->_pageMarges[$mY][1]>$xLeft) {
                unset($this->_pageMarges[$mY]);
            }
        }

        // save the new Top and Bottom margins
        $this->_pageMarges[$yTop] = $oldTop;
        $this->_pageMarges[$yBottom] = $oldBottom;

        // sort the margins
        ksort($this->_pageMarges);

        // we are just after float
        $this->_isAfterFloat = true;
    }

    /**
     * Save old margins (push), and set new ones
     *
     * @access protected
     * @param  float  $ml left margin
     * @param  float  $mt top margin
     * @param  float  $mr right margin
     */
    protected function _saveMargin($ml, $mt, $mr)
    {
        // save old margins
        $this->_marges[] = array(
            'l' => $this->pdf->getlMargin(),
            't' => $this->pdf->gettMargin(),
            'r' => $this->pdf->getrMargin(),
            'page' => $this->_pageMarges
        );

        // set new ones
        $this->pdf->SetMargins($ml, $mt, $mr);

        // prepare for float margins
        $this->_pageMarges = array();
        $this->_pageMarges[floor($mt*100)] = array($ml, $this->pdf->getW()-$mr);
    }

    /**
     * load the last saved margins (pop)
     *
     * @access protected
     */
    protected function _loadMargin()
    {
        $old = array_pop($this->_marges);
        if ($old) {
            $ml = $old['l'];
            $mt = $old['t'];
            $mr = $old['r'];
            $mP = $old['page'];
        } else {
            $ml = $this->_margeLeft;
            $mt = 0;
            $mr = $this->_margeRight;
            $mP = array($mt => array($ml, $this->pdf->getW()-$mr));
        }

        $this->pdf->SetMargins($ml, $mt, $mr);
        $this->_pageMarges = $mP;
    }

    /**
     * save the current maxs (push)
     *
     * @access protected
     */
    protected function _saveMax()
    {
        $this->_maxSave[] = array($this->_maxX, $this->_maxY, $this->_maxH, $this->_maxE);
    }

    /**
     * load the last saved current maxs (pop)
     *
     * @access protected
     */
    protected function _loadMax()
    {
        $old = array_pop($this->_maxSave);

        if ($old) {
            $this->_maxX = $old[0];
            $this->_maxY = $old[1];
            $this->_maxH = $old[2];
            $this->_maxE = $old[3];
        } else {
            $this->_maxX = 0;
            $this->_maxY = 0;
            $this->_maxH = 0;
            $this->_maxE = 0;
        }
    }

    /**
     * draw the PDF header with the HTML in page_header
     *
     * @access protected
     */
    protected function _setPageHeader()
    {
        if (!count($this->_subHEADER)) {
            return false;
        }

        if (in_array($this->pdf->getPage(), $this->_hideHeader)) {
            return false;
        }

        $oldParsePos = $this->_parsePos;
        $oldParseCode = $this->parsingHtml->code;

        $this->_parsePos = 0;
        $this->parsingHtml->code = $this->_subHEADER;
        $this->_makeHTMLcode();

        $this->_parsePos = $oldParsePos;
        $this->parsingHtml->code = $oldParseCode;
    }

    /**
     * draw the PDF footer with the HTML in page_footer
     *
     * @access protected
     */
    protected function _setPageFooter()
    {
        if (!count($this->_subFOOTER)) {
            return false;
        }

        if (in_array($this->pdf->getPage(), $this->_hideFooter)) {
            return false;
        }

        $oldParsePos = $this->_parsePos;
        $oldParseCode = $this->parsingHtml->code;

        $this->_parsePos = 0;
        $this->parsingHtml->code = $this->_subFOOTER;
        $this->_isInFooter = true;
        $this->_makeHTMLcode();
        $this->_isInFooter = false;

        $this->_parsePos = $oldParsePos;
        $this->parsingHtml->code = $oldParseCode;
    }

    /**
     * new line, with a specific height
     *
     * @access protected
     * @param float   $h
     * @param integer $curr real current position in the text, if new line in the write of a text
     */
    protected function _setNewLine($h, $curr = null)
    {
        $this->pdf->Ln($h);
        $this->_setNewPositionForNewLine($curr);
    }

    /**
     * calculate the start position of the next line,  depending on the text-align
     *
     * @access protected
     * @param  integer $curr real current position in the text, if new line in the write of a text
     */
    protected function _setNewPositionForNewLine($curr = null)
    {
        // get the margins for the current line
        list($lx, $rx) = $this->_getMargins($this->pdf->GetY());
        $this->pdf->SetX($lx);
        $wMax = $rx-$lx;
        $this->_currentH = 0;

        // if subPart => return because align left
        if ($this->_subPart || $this->_isSubPart || $this->_isForOneLine) {
            $this->pdf->setWordSpacing(0);
            return null;
        }

        // create the sub object
        $sub = $this->createSubHTML();
        $sub->_saveMargin(0, 0, $sub->pdf->getW()-$wMax);
        $sub->_isForOneLine = true;
        $sub->_parsePos = $this->_parsePos;
        $sub->parsingHtml->code = $this->parsingHtml->getCloneCodes();

        // if $curr => adapt the current position of the parsing
        if ($curr !== null && $sub->parsingHtml->code[$this->_parsePos]->getName() === 'write') {
            $txt = $sub->parsingHtml->code[$this->_parsePos]->getParam('txt');
            $txt = str_replace('[[page_cu]]', $sub->pdf->getMyNumPage($this->_page), $txt);
            $sub->parsingHtml->code[$this->_parsePos]->setParam('txt', substr($txt, $curr + 1));
        } else {
            $sub->_parsePos++;
        }

        // for each element of the parsing => load the action
        $res = null;
        $amountHtmlCodes = count($sub->parsingHtml->code);
        for ($sub->_parsePos; $sub->_parsePos < $amountHtmlCodes; $sub->_parsePos++) {
            $action = $sub->parsingHtml->code[$sub->_parsePos];
            $res = $sub->_executeAction($action);
            if (!$res) {
                break;
            }
        }

        $w = $sub->_maxX; // max width
        $h = $sub->_maxH; // max height
        $e = ($res === null ? $sub->_maxE : 0); // maxnumber of elemets on the line

        // destroy the sub HTML
        $this->_destroySubHTML($sub);

        // adapt the start of the line, depending on the text-align
        if ($this->parsingCss->value['text-align'] === 'center') {
            $this->pdf->SetX(($rx+$this->pdf->GetX()-$w)*0.5-0.01);
        } elseif ($this->parsingCss->value['text-align'] === 'right') {
            $this->pdf->SetX($rx-$w-0.01);
        } else {
            $this->pdf->SetX($lx);
        }

        // set the height of the line
        $this->_currentH = $h;

        // if justify => set the word spacing
        if ($this->parsingCss->value['text-align'] === 'justify' && $e>1) {
            $this->pdf->setWordSpacing(($wMax-$w)/($e-1));
        } else {
            $this->pdf->setWordSpacing(0);
        }
    }

    /**
     * prepare self::$_subobj (used for create the sub Html2Pdf objects
     *
     * @access protected
     */
    protected function _prepareSubObj()
    {
        $pdf = null;

        // create the sub object
        self::$_subobj = new Html2Pdf(
            $this->_orientation,
            $this->_format,
            $this->_langue,
            $this->_unicode,
            $this->_encoding,
            array($this->_defaultLeft,$this->_defaultTop,$this->_defaultRight,$this->_defaultBottom),
            $this->_pdfa
        );

        // init
        self::$_subobj->setSentenceMaxLines($this->_sentenceMaxLines);
        self::$_subobj->setTestTdInOnePage($this->_testTdInOnepage);
        self::$_subobj->setTestIsImage($this->_testIsImage);
        self::$_subobj->setDefaultFont($this->_defaultFont);
        self::$_subobj->setFallbackImage($this->_fallbackImage);
        self::$_subobj->parsingCss->css            = &$this->parsingCss->css;
        self::$_subobj->parsingCss->cssKeys        = &$this->parsingCss->cssKeys;

        // add all the extensions
        foreach ($this->extensions as $extension) {
            self::$_subobj->addExtension($extension);
        }

        // clone font from the original PDF
        self::$_subobj->pdf->cloneFontFrom($this->pdf);

        // remove the link to the parent
        self::$_subobj->parsingCss->setPdfParent($pdf);
    }

    /**
     * create a sub Html2Pdf, to calculate the multi-tables
     *
     * @return Html2Pdf
     */
    protected function createSubHTML()
    {
        // prepare the subObject, if never prepare before
        if (self::$_subobj === null) {
            $this->_prepareSubObj();
        }

        // calculate the width to use
        if ($this->parsingCss->value['width']) {
            $marge = $this->parsingCss->value['padding']['l'] + $this->parsingCss->value['padding']['r'];
            $marge+= $this->parsingCss->value['border']['l']['width'] + $this->parsingCss->value['border']['r']['width'];
            $marge = $this->pdf->getW() - $this->parsingCss->value['width'] + $marge;
        } else {
            $marge = $this->_margeLeft+$this->_margeRight;
        }

        // BUGFIX : we have to call the method, because of a bug in php 5.1.6
        self::$_subobj->pdf->getPage();

        // clone the sub object
        $subHtml = clone self::$_subobj;
        $subHtml->parsingCss->table = $this->parsingCss->table;
        $subHtml->parsingCss->value = $this->parsingCss->value;
        $subHtml->initSubHtml(
            $this->_format,
            $this->_orientation,
            $marge,
            $this->_page,
            $this->_defList,
            $this->pdf->getMyLastPageGroup(),
            $this->pdf->getMyLastPageGroupNb()
        );

        return $subHtml;
    }

    /**
     * destroy a subHtml2Pdf
     *
     * @access protected
     */
    protected function _destroySubHTML(&$subHtml)
    {
        unset($subHtml);
        $subHtml = null;
    }

    /**
     * Convert an arabic number into a roman number
     *
     * @access protected
     * @param  integer $nbArabic
     * @return string  $nbRoman
     */
    protected function _listeArab2Rom($nbArabic)
    {
        $nbBaseTen  = array('I','X','C','M');
        $nbBaseFive = array('V','L','D');
        $nbRoman    = '';

        if ($nbArabic<1) {
            return $nbArabic;
        }
        if ($nbArabic>3999) {
            return $nbArabic;
        }

        for ($i=3; $i>=0; $i--) {
            $digit=floor($nbArabic/pow(10, $i));
            if ($digit>=1) {
                $nbArabic -= $digit*pow(10, $i);
                if ($digit<=3) {
                    for ($j=$digit; $j>=1; $j--) {
                        $nbRoman .= $nbBaseTen[$i];
                    }
                } elseif ($digit == 9) {
                    $nbRoman .= $nbBaseTen[$i].$nbBaseTen[$i+1];
                } elseif ($digit == 4) {
                    $nbRoman .= $nbBaseTen[$i].$nbBaseFive[$i];
                } else {
                    $nbRoman .= $nbBaseFive[$i];
                    for ($j=$digit-5; $j>=1; $j--) {
                        $nbRoman .= $nbBaseTen[$i];
                    }
                }
            }
        }
        return $nbRoman;
    }

    /**
     * add a LI to the current level
     *
     * @access protected
     */
    protected function _listeAddLi()
    {
        $this->_defList[count($this->_defList)-1]['nb']++;
    }

    /**
     * get the width to use for the column of the list
     *
     * @access protected
     * @return string $width
     */
    protected function _listeGetWidth()
    {
        return '7mm';
    }

    /**
     * get the padding to use for the column of the list
     *
     * @access protected
     * @return string $padding
     */
    protected function _listeGetPadding()
    {
        return '1mm';
    }

    /**
     * get the information of the li on the current level
     *
     * @access protected
     * @return array(fontName, small size, string)
     */
    protected function _listeGetLi()
    {
        $im = $this->_defList[count($this->_defList)-1]['img'];
        $st = $this->_defList[count($this->_defList)-1]['style'];
        $nb = $this->_defList[count($this->_defList)-1]['nb'];
        $up = (substr($st, 0, 6) === 'upper-');

        if ($im) {
            return array(false, false, $im);
        }

        switch ($st) {
            case 'none':
                return array('helvetica', true, ' ');

            case 'upper-alpha':
            case 'lower-alpha':
                $str = '';
                while ($nb>26) {
                    $str = chr(96+$nb%26).$str;
                    $nb = floor($nb/26);
                }
                $str = chr(96+$nb).$str;

                return array('helvetica', false, ($up ? strtoupper($str) : $str).'.');

            case 'upper-roman':
            case 'lower-roman':
                $str = $this->_listeArab2Rom($nb);

                return array('helvetica', false, ($up ? strtoupper($str) : $str).'.');

            case 'decimal':
                return array('helvetica', false, $nb.'.');

            case 'square':
                return array('zapfdingbats', true, chr(110));

            case 'circle':
                return array('zapfdingbats', true, chr(109));

            case 'disc':
            default:
                return array('zapfdingbats', true, chr(108));
        }
    }

    /**
     * add a level to the list
     *
     * @access protected
     * @param  string $type  : ul, ol
     * @param  string $style : lower-alpha, ...
     * @param  string $img
     */
    protected function _listeAddLevel($type = 'ul', $style = '', $img = null, $start = null)
    {
        // get the url of the image, if we want to use a image
        if ($img) {
            if (preg_match('/^url\(([^)]+)\)$/isU', trim($img), $match)) {
                $img = $match[1];
            } else {
                $img = null;
            }
        } else {
            $img = null;
        }

        // prepare the datas
        if (!in_array($type, array('ul', 'ol'))) {
            $type = 'ul';
        }
        if (!in_array($style, array('lower-alpha', 'upper-alpha', 'upper-roman', 'lower-roman', 'decimal', 'square', 'circle', 'disc', 'none'))) {
            $style = '';
        }

        if (!$style) {
            if ($type === 'ul') {
                $style = 'disc';
            } else {
                $style = 'decimal';
            }
        }

        if (is_null($start) || (int) $start<1) {
            $start=0;
        } else {
            $start--;
        }

        // add the new level
        $this->_defList[count($this->_defList)] = array('style' => $style, 'nb' => $start, 'img' => $img);
    }

    /**
     * remove a level from the list
     *
     * @access protected
     */
    protected function _listeDelLevel()
    {
        if (count($this->_defList)) {
            unset($this->_defList[count($this->_defList)-1]);
            $this->_defList = array_values($this->_defList);
        }
    }

    /**
     * execute the actions to convert the html
     *
     * @access protected
     */
    protected function _makeHTMLcode()
    {
        $amountHtmlCode = count($this->parsingHtml->code);

        // foreach elements of the parsing
        for ($this->_parsePos=0; $this->_parsePos<$amountHtmlCode; $this->_parsePos++) {

            // get the action to do
            $action = $this->parsingHtml->code[$this->_parsePos];

            // if it is a opening of table / ul / ol
            if (in_array($action->getName(), array('table', 'ul', 'ol')) && !$action->isClose()) {

                //  we will work as a sub HTML to calculate the size of the element
                $this->_subPart = true;

                // get the name of the opening tag
                $tagOpen = $action->getName();

                // save the actual pos on the parsing
                $this->_tempPos = $this->_parsePos;

                // foreach elements, while we are in the opened tag
                while (isset($this->parsingHtml->code[$this->_tempPos]) && !($this->parsingHtml->code[$this->_tempPos]->getName() == $tagOpen && $this->parsingHtml->code[$this->_tempPos]->isClose())) {
                    // make the action
                    $this->_executeAction($this->parsingHtml->code[$this->_tempPos]);
                    $this->_tempPos++;
                }

                // execute the closure of the tag
                if (isset($this->parsingHtml->code[$this->_tempPos])) {
                    $this->_executeAction($this->parsingHtml->code[$this->_tempPos]);
                }

                // end of the sub part
                $this->_subPart = false;
            }

            // execute the action
            $this->_executeAction($action);
        }
    }

    /**
     * execute the action from the parsing
     *
     * @param Node $action
     */
    protected function _executeAction(Node $action)
    {
        $name = strtoupper($action->getName());

        if ($this->_firstPage && $name !== 'PAGE' && !$action->isClose()) {
            $this->_setNewPage();
        }

        // properties of the action
        $properties = $action->getParams();

        // name of the action (old method)
        $fnc = ($action->isClose() ? '_tag_close_' : '_tag_open_').$name;

        $tagObject = $this->getTagObject($action->getName());

        if (!is_null($tagObject)) {
            if ($action->isClose()) {
                $res = $tagObject->close($properties);
            } else {
                $res = $tagObject->open($properties);
            }
        } elseif (is_callable(array($this, $fnc))) {
            $res = $this->{$fnc}($properties);
        } else {
            $e = new HtmlParsingException(
                'The html tag ['.$action->getName().'] is not known by Html2Pdf. '.
                'You can create it and push it on the Html2Pdf GitHub project.'
            );
            $e->setInvalidTag($action->getName());
            $e->setHtmlLine($action->getLine());
            throw $e;
        }

        // save the name of the action
        $this->_previousCall = $fnc;

        // return the result
        return $res;
    }

    /**
     * get the position of the element on the current line, depending on its height
     *
     * @access protected
     * @param  float $h
     * @return float
     */
    protected function _getElementY($h)
    {
        if ($this->_subPart || $this->_isSubPart || !$this->_currentH || $this->_currentH<$h) {
            return 0;
        }

        return ($this->_currentH-$h)*0.8;
    }

    /**
     * make a break line
     *
     * @access protected
     * @param  float $h current line height
     * @param  integer $curr real current position in the text, if new line in the write of a text
     */
    protected function _makeBreakLine($h, $curr = null)
    {
        if ($h) {
            if (($this->pdf->GetY()+$h<$this->pdf->getH() - $this->pdf->getbMargin()) || $this->_isInOverflow || $this->_isInFooter) {
                $this->_setNewLine($h, $curr);
            } else {
                $this->_setNewPage(null, '', null, $curr);
            }
        } else {
            $this->_setNewPositionForNewLine($curr);
        }

        $this->_maxH = 0;
        $this->_maxE = 0;
    }

    /**
     * display an image
     *
     * @access protected
     * @param  string $src
     * @param  boolean $subLi if true=image of a list
     * @return boolean depending on "isForOneLine"
     */
    protected function _drawImage($src, $subLi = false)
    {
        // get the size of the image
        // WARNING : if URL, "allow_url_fopen" must turned to "on" in php.ini
        $infos=@getimagesize($src);

        // if the image does not exist, or can not be loaded
        if (!is_array($infos) || count($infos)<2) {
            if ($this->_testIsImage) {
                $e = new ImageException('Unable to get the size of the image ['.$src.']');
                $e->setImage($src);
                throw $e;
            }

            // display a gray rectangle
            $src = null;
            $infos = array(16, 16);

            // if we have a fallback Image, we use it
            if ($this->_fallbackImage) {
                $src = $this->_fallbackImage;
                $infos = @getimagesize($src);

                if (count($infos)<2) {
                    $e = new ImageException('Unable to get the size of the fallback image ['.$src.']');
                    $e->setImage($src);
                    throw $e;
                }
            }
        }

        // convert the size of the image in the unit of the PDF
        $imageWidth = $infos[0]/$this->pdf->getK();
        $imageHeight = $infos[1]/$this->pdf->getK();

        $ratio = $imageWidth / $imageHeight;

        // calculate the size from the css style
        if ($this->parsingCss->value['width'] && $this->parsingCss->value['height']) {
            $w = $this->parsingCss->value['width'];
            $h = $this->parsingCss->value['height'];
        } elseif ($this->parsingCss->value['width']) {
            $w = $this->parsingCss->value['width'];
            $h = $w / $ratio;
        } elseif ($this->parsingCss->value['height']) {
            $h = $this->parsingCss->value['height'];
            $w = $h * $ratio;
        } else {
            // convert px to pt
            $w = 72./96.*$imageWidth;
            $h = 72./96.*$imageHeight;
        }

        if (isset($this->parsingCss->value['max-width']) && $this->parsingCss->value['max-width'] < $w) {
            $w = $this->parsingCss->value['max-width'];
            if (!$this->parsingCss->value['height']) {
                // reprocess the height if not constrained
                $h = $w / $ratio;
            }
        }
        if (isset($this->parsingCss->value['max-height']) && $this->parsingCss->value['max-height'] < $h) {
            $h = $this->parsingCss->value['max-height'];
            if (!$this->parsingCss->value['width']) {
                // reprocess the width if not constrained
                $w = $h * $ratio;
            }
        }

        // are we in a float
        $float = $this->parsingCss->getFloat();

        // if we are in a float, but if something else if on the line
        // => make the break line (false if we are in "_isForOneLine" mode)
        if ($float && $this->_maxH && !$this->_tag_open_BR(array())) {
            return false;
        }

        // position of the image
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();

        // if the image can not be put on the current line => new line
        if (!$float && ($x + $w>$this->pdf->getW() - $this->pdf->getrMargin()) && $this->_maxH) {
            if ($this->_isForOneLine) {
                return false;
            }

            // set the new line
            $hnl = max($this->_maxH, $this->parsingCss->getLineHeight());
            $this->_setNewLine($hnl);

            // get the new position
            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
        }

        // if the image can not be put on the current page
        if (($y + $h>$this->pdf->getH() - $this->pdf->getbMargin()) && !$this->_isInOverflow) {
            // new page
            $this->_setNewPage();

            // get the new position
            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
        }

        // correction for display the image of a list
        $hT = 0.80*$this->parsingCss->value['font-size'];
        if ($subLi && $h<$hT) {
            $y+=($hT-$h);
        }

        // add the margin top
        $yc = $y-$this->parsingCss->value['margin']['t'];

        // get the width and the position of the parent
        $old = $this->parsingCss->getOldValues();
        if ($old['width']) {
            $parentWidth = $old['width'];
            $parentX = $x;
        } else {
            $parentWidth = $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();
            $parentX = $this->pdf->getlMargin();
        }

        // if we are in a gloat => adapt the parent position and width
        if ($float) {
            list($lx, $rx) = $this->_getMargins($yc);
            $parentX = $lx;
            $parentWidth = $rx-$lx;
        }

        // calculate the position of the image, if align to the right
        if ($parentWidth>$w && $float !== 'left') {
            if ($float === 'right' || $this->parsingCss->value['text-align'] === 'li_right') {
                $x = $parentX + $parentWidth - $w-$this->parsingCss->value['margin']['r']-$this->parsingCss->value['margin']['l'];
            }
        }

        // display the image
        if (!$this->_subPart && !$this->_isSubPart) {
            if ($src) {
                $this->pdf->Image($src, $x, $y, $w, $h, '', $this->_isInLink);
            } else {
                // rectangle if the image can not be loaded
                $this->pdf->SetFillColorArray(array(240, 220, 220));
                $this->pdf->Rect($x, $y, $w, $h, 'F');
            }
        }

        // apply the margins
        $x-= $this->parsingCss->value['margin']['l'];
        $y-= $this->parsingCss->value['margin']['t'];
        $w+= $this->parsingCss->value['margin']['l'] + $this->parsingCss->value['margin']['r'];
        $h+= $this->parsingCss->value['margin']['t'] + $this->parsingCss->value['margin']['b'];

        if ($float === 'left') {
            // save the current max
            $this->_maxX = max($this->_maxX, $x+$w);
            $this->_maxY = max($this->_maxY, $y+$h);

            // add the image to the margins
            $this->_addMargins($float, $x, $y, $x+$w, $y+$h);

            // get the new position
            list($lx, $rx) = $this->_getMargins($yc);
            $this->pdf->SetXY($lx, $yc);
        } elseif ($float === 'right') {
            // save the current max. We don't save the X because it is not the real max of the line
            $this->_maxY = max($this->_maxY, $y+$h);

            // add the image to the margins
            $this->_addMargins($float, $x, $y, $x+$w, $y+$h);

            // get the new position
            list($lx, $rx) = $this->_getMargins($yc);
            $this->pdf->SetXY($lx, $yc);
        } else {
            // set the new position at the end of the image
            $this->pdf->SetX($x+$w);

            // save the current max
            $this->_maxX = max($this->_maxX, $x+$w);
            $this->_maxY = max($this->_maxY, $y+$h);
            $this->_maxH = max($this->_maxH, $h);
        }

        return true;
    }

    /**
     * draw a rectangle
     *
     * @access protected
     * @param  float $x
     * @param  float $y
     * @param  float $w
     * @param  float $h
     * @param  array $border
     * @param  float $padding - internal margin of the rectangle => not used, but...
     * @param  float $margin  - external margin of the rectangle
     * @param  array $background
     * @return boolean
     */
    protected function _drawRectangle($x, $y, $w, $h, $border, $padding, $margin, $background)
    {
        // if we are in a subpart or if height is null => return false
        if ($this->_subPart || $this->_isSubPart || $h === null) {
            return false;
        }

        // add the margin
        $x+= $margin;
        $y+= $margin;
        $w-= $margin*2;
        $h-= $margin*2;

        // get the radius of the border
        $outTL = $border['radius']['tl'];
        $outTR = $border['radius']['tr'];
        $outBR = $border['radius']['br'];
        $outBL = $border['radius']['bl'];

        // prepare the out radius
        $outTL = ($outTL[0] && $outTL[1]) ? $outTL : null;
        $outTR = ($outTR[0] && $outTR[1]) ? $outTR : null;
        $outBR = ($outBR[0] && $outBR[1]) ? $outBR : null;
        $outBL = ($outBL[0] && $outBL[1]) ? $outBL : null;

        // prepare the in radius
        $inTL = $outTL;
        $inTR = $outTR;
        $inBR = $outBR;
        $inBL = $outBL;

        if (is_array($inTL)) {
            $inTL[0]-= $border['l']['width'];
            $inTL[1]-= $border['t']['width'];
        }
        if (is_array($inTR)) {
            $inTR[0]-= $border['r']['width'];
            $inTR[1]-= $border['t']['width'];
        }
        if (is_array($inBR)) {
            $inBR[0]-= $border['r']['width'];
            $inBR[1]-= $border['b']['width'];
        }
        if (is_array($inBL)) {
            $inBL[0]-= $border['l']['width'];
            $inBL[1]-= $border['b']['width'];
        }

        if ($inTL[0]<=0 || $inTL[1]<=0) {
            $inTL = null;
        }
        if ($inTR[0]<=0 || $inTR[1]<=0) {
            $inTR = null;
        }
        if ($inBR[0]<=0 || $inBR[1]<=0) {
            $inBR = null;
        }
        if ($inBL[0]<=0 || $inBL[1]<=0) {
            $inBL = null;
        }

        // prepare the background color
        $pdfStyle = '';
        if ($background['color']) {
            $this->pdf->SetFillColorArray($background['color']);
            $pdfStyle.= 'F';
        }

        // if we have a background to fill => fill it with a path (because of the radius)
        if ($pdfStyle) {
            $this->pdf->clippingPathStart($x, $y, $w, $h, $outTL, $outTR, $outBL, $outBR);
            $this->pdf->Rect($x, $y, $w, $h, $pdfStyle);
            $this->pdf->clippingPathStop();
        }

        // prepare the background image
        if ($background['image']) {
            $iName      = $background['image'];
            $iPosition  = $background['position'] !== null ? $background['position'] : array(0, 0);
            $iRepeat    = $background['repeat'] !== null   ? $background['repeat']   : array(true, true);

            // size of the background without the borders
            $bX = $x;
            $bY = $y;
            $bW = $w;
            $bH = $h;

            if ($border['b']['width']) {
                $bH-= $border['b']['width'];
            }
            if ($border['l']['width']) {
                $bW-= $border['l']['width'];
                $bX+= $border['l']['width'];
            }
            if ($border['t']['width']) {
                $bH-= $border['t']['width'];
                $bY+= $border['t']['width'];
            }
            if ($border['r']['width']) {
                $bW-= $border['r']['width'];
            }

            // get the size of the image
            // WARNING : if URL, "allow_url_fopen" must turned to "on" in php.ini
            $imageInfos=@getimagesize($iName);

            // if the image can not be loaded
            if (!is_array($imageInfos) || count($imageInfos)<2) {
                if ($this->_testIsImage) {
                    $e = new ImageException('Unable to get the size of the image ['.$iName.']');
                    $e->setImage($iName);
                    throw $e;
                }
            } else {
                // convert the size of the image from pixel to the unit of the PDF
                $imageWidth    = 72./96.*$imageInfos[0]/$this->pdf->getK();
                $imageHeight    = 72./96.*$imageInfos[1]/$this->pdf->getK();

                // prepare the position of the backgroung
                if ($iRepeat[0]) {
                    $iPosition[0] = $bX;
                } elseif (preg_match('/^([-]?[0-9\.]+)%/isU', $iPosition[0], $match)) {
                    $iPosition[0] = $bX + $match[1]*($bW-$imageWidth)/100;
                } else {
                    $iPosition[0] = $bX+$iPosition[0];
                }

                if ($iRepeat[1]) {
                    $iPosition[1] = $bY;
                } elseif (preg_match('/^([-]?[0-9\.]+)%/isU', $iPosition[1], $match)) {
                    $iPosition[1] = $bY + $match[1]*($bH-$imageHeight)/100;
                } else {
                    $iPosition[1] = $bY+$iPosition[1];
                }

                $imageXmin = $bX;
                $imageXmax = $bX+$bW;
                $imageYmin = $bY;
                $imageYmax = $bY+$bH;

                if (!$iRepeat[0] && !$iRepeat[1]) {
                    $imageXmin =     $iPosition[0];
                    $imageXmax =     $iPosition[0]+$imageWidth;
                    $imageYmin =     $iPosition[1];
                    $imageYmax =     $iPosition[1]+$imageHeight;
                } elseif ($iRepeat[0] && !$iRepeat[1]) {
                    $imageYmin =     $iPosition[1];
                    $imageYmax =     $iPosition[1]+$imageHeight;
                } elseif (!$iRepeat[0] && $iRepeat[1]) {
                    $imageXmin =     $iPosition[0];
                    $imageXmax =     $iPosition[0]+$imageWidth;
                }

                // build the path to display the image (because of radius)
                $this->pdf->clippingPathStart($bX, $bY, $bW, $bH, $inTL, $inTR, $inBL, $inBR);

                // repeat the image
                for ($iY=$imageYmin; $iY<$imageYmax; $iY+=$imageHeight) {
                    for ($iX=$imageXmin; $iX<$imageXmax; $iX+=$imageWidth) {
                        $cX = null;
                        $cY = null;
                        $cW = $imageWidth;
                        $cH = $imageHeight;
                        if ($imageYmax-$iY<$imageHeight) {
                            $cX = $iX;
                            $cY = $iY;
                            $cH = $imageYmax-$iY;
                        }
                        if ($imageXmax-$iX<$imageWidth) {
                            $cX = $iX;
                            $cY = $iY;
                            $cW = $imageXmax-$iX;
                        }

                        $this->pdf->Image($iName, $iX, $iY, $imageWidth, $imageHeight, '', '');
                    }
                }

                // end of the path
                $this->pdf->clippingPathStop();
            }
        }

        // adding some loose (0.01mm)
        $loose = 0.01;
        $x-= $loose;
        $y-= $loose;
        $w+= 2.*$loose;
        $h+= 2.*$loose;
        if ($border['l']['width']) {
            $border['l']['width']+= 2.*$loose;
        }
        if ($border['t']['width']) {
            $border['t']['width']+= 2.*$loose;
        }
        if ($border['r']['width']) {
            $border['r']['width']+= 2.*$loose;
        }
        if ($border['b']['width']) {
            $border['b']['width']+= 2.*$loose;
        }

        // prepare the test on borders
        $testBl = ($border['l']['width'] && $border['l']['color'][0] !== null);
        $testBt = ($border['t']['width'] && $border['t']['color'][0] !== null);
        $testBr = ($border['r']['width'] && $border['r']['color'][0] !== null);
        $testBb = ($border['b']['width'] && $border['b']['color'][0] !== null);

        // draw the radius bottom-left
        if (is_array($outBL) && ($testBb || $testBl)) {
            if ($inBL) {
                $courbe = array();
                $courbe[] = $x+$outBL[0];
                $courbe[] = $y+$h;
                $courbe[] = $x;
                $courbe[] = $y+$h-$outBL[1];
                $courbe[] = $x+$outBL[0];
                $courbe[] = $y+$h-$border['b']['width'];
                $courbe[] = $x+$border['l']['width'];
                $courbe[] = $y+$h-$outBL[1];
                $courbe[] = $x+$outBL[0];
                $courbe[] = $y+$h-$outBL[1];
            } else {
                $courbe = array();
                $courbe[] = $x+$outBL[0];
                $courbe[] = $y+$h;
                $courbe[] = $x;
                $courbe[] = $y+$h-$outBL[1];
                $courbe[] = $x+$border['l']['width'];
                $courbe[] = $y+$h-$border['b']['width'];
                $courbe[] = $x+$outBL[0];
                $courbe[] = $y+$h-$outBL[1];
            }
            $this->_drawCurve($courbe, $border['l']['color']);
        }

        // draw the radius left-top
        if (is_array($outTL) && ($testBt || $testBl)) {
            if ($inTL) {
                $courbe = array();
                $courbe[] = $x;
                $courbe[] = $y+$outTL[1];
                $courbe[] = $x+$outTL[0];
                $courbe[] = $y;
                $courbe[] = $x+$border['l']['width'];
                $courbe[] = $y+$outTL[1];
                $courbe[] = $x+$outTL[0];
                $courbe[] = $y+$border['t']['width'];
                $courbe[] = $x+$outTL[0];
                $courbe[] = $y+$outTL[1];
            } else {
                $courbe = array();
                $courbe[] = $x;
                $courbe[] = $y+$outTL[1];
                $courbe[] = $x+$outTL[0];
                $courbe[] = $y;
                $courbe[] = $x+$border['l']['width'];
                $courbe[] = $y+$border['t']['width'];
                $courbe[] = $x+$outTL[0];
                $courbe[] = $y+$outTL[1];
            }
            $this->_drawCurve($courbe, $border['t']['color']);
        }

        // draw the radius top-right
        if (is_array($outTR) && ($testBt || $testBr)) {
            if ($inTR) {
                $courbe = array();
                $courbe[] = $x+$w-$outTR[0];
                $courbe[] = $y;
                $courbe[] = $x+$w;
                $courbe[] = $y+$outTR[1];
                $courbe[] = $x+$w-$outTR[0];
                $courbe[] = $y+$border['t']['width'];
                $courbe[] = $x+$w-$border['r']['width'];
                $courbe[] = $y+$outTR[1];
                $courbe[] = $x+$w-$outTR[0];
                $courbe[] = $y+$outTR[1];
            } else {
                $courbe = array();
                $courbe[] = $x+$w-$outTR[0];
                $courbe[] = $y;
                $courbe[] = $x+$w;
                $courbe[] = $y+$outTR[1];
                $courbe[] = $x+$w-$border['r']['width'];
                $courbe[] = $y+$border['t']['width'];
                $courbe[] = $x+$w-$outTR[0];
                $courbe[] = $y+$outTR[1];
            }
            $this->_drawCurve($courbe, $border['r']['color']);
        }

        // draw the radius right-bottom
        if (is_array($outBR) && ($testBb || $testBr)) {
            if ($inBR) {
                $courbe = array();
                $courbe[] = $x+$w;
                $courbe[] = $y+$h-$outBR[1];
                $courbe[] = $x+$w-$outBR[0];
                $courbe[] = $y+$h;
                $courbe[] = $x+$w-$border['r']['width'];
                $courbe[] = $y+$h-$outBR[1];
                $courbe[] = $x+$w-$outBR[0];
                $courbe[] = $y+$h-$border['b']['width'];
                $courbe[] = $x+$w-$outBR[0];
                $courbe[] = $y+$h-$outBR[1];
            } else {
                $courbe = array();
                $courbe[] = $x+$w;
                $courbe[] = $y+$h-$outBR[1];
                $courbe[] = $x+$w-$outBR[0];
                $courbe[] = $y+$h;
                $courbe[] = $x+$w-$border['r']['width'];
                $courbe[] = $y+$h-$border['b']['width'];
                $courbe[] = $x+$w-$outBR[0];
                $courbe[] = $y+$h-$outBR[1];
            }
            $this->_drawCurve($courbe, $border['b']['color']);
        }

        // draw the left border
        if ($testBl) {
            $pt = array();
            $pt[] = $x;
            $pt[] = $y+$h;
            $pt[] = $x;
            $pt[] = $y+$h-$border['b']['width'];
            $pt[] = $x;
            $pt[] = $y+$border['t']['width'];
            $pt[] = $x;
            $pt[] = $y;
            $pt[] = $x+$border['l']['width'];
            $pt[] = $y+$border['t']['width'];
            $pt[] = $x+$border['l']['width'];
            $pt[] = $y+$h-$border['b']['width'];

            $bord = 3;
            if (is_array($outBL)) {
                $bord-=1;
                $pt[3] -= $outBL[1] - $border['b']['width'];
                if ($inBL) {
                    $pt[11]-= $inBL[1];
                }
                unset($pt[0]);
                unset($pt[1]);
            }
            if (is_array($outTL)) {
                $bord-=2;
                $pt[5] += $outTL[1]-$border['t']['width'];
                if ($inTL) {
                    $pt[9] += $inTL[1];
                }
                unset($pt[6]);
                unset($pt[7]);
            }

            $pt = array_values($pt);
            $this->_drawLine($pt, $border['l']['color'], $border['l']['type'], $border['l']['width'], $bord);
        }

        // draw the top border
        if ($testBt) {
            $pt = array();
            $pt[] = $x;
            $pt[] = $y;
            $pt[] = $x+$border['l']['width'];
            $pt[] = $y;
            $pt[] = $x+$w-$border['r']['width'];
            $pt[] = $y;
            $pt[] = $x+$w;
            $pt[] = $y;
            $pt[] = $x+$w-$border['r']['width'];
            $pt[] = $y+$border['t']['width'];
            $pt[] = $x+$border['l']['width'];
            $pt[] = $y+$border['t']['width'];

            $bord = 3;
            if (is_array($outTL)) {
                $bord-=1;
                $pt[2] += $outTL[0] - $border['l']['width'];
                if ($inTL) {
                    $pt[10]+= $inTL[0];
                }
                unset($pt[0]);
                unset($pt[1]);
            }
            if (is_array($outTR)) {
                $bord-=2;
                $pt[4] -= $outTR[0] - $border['r']['width'];
                if ($inTR) {
                    $pt[8] -= $inTR[0];
                }
                unset($pt[6]);
                unset($pt[7]);
            }

            $pt = array_values($pt);
            $this->_drawLine($pt, $border['t']['color'], $border['t']['type'], $border['t']['width'], $bord);
        }

        // draw the right border
        if ($testBr) {
            $pt = array();
            $pt[] = $x+$w;
            $pt[] = $y;
            $pt[] = $x+$w;
            $pt[] = $y+$border['t']['width'];
            $pt[] = $x+$w;
            $pt[] = $y+$h-$border['b']['width'];
            $pt[] = $x+$w;
            $pt[] = $y+$h;
            $pt[] = $x+$w-$border['r']['width'];
            $pt[] = $y+$h-$border['b']['width'];
            $pt[] = $x+$w-$border['r']['width'];
            $pt[] = $y+$border['t']['width'];

            $bord = 3;
            if (is_array($outTR)) {
                $bord-=1;
                $pt[3] += $outTR[1] - $border['t']['width'];
                if ($inTR) {
                    $pt[11]+= $inTR[1];
                }
                unset($pt[0]);
                unset($pt[1]);
            }
            if (is_array($outBR)) {
                $bord-=2;
                $pt[5] -= $outBR[1] - $border['b']['width'];
                if ($inBR) {
                    $pt[9] -= $inBR[1];
                }
                unset($pt[6]);
                unset($pt[7]);
            }

            $pt = array_values($pt);
            $this->_drawLine($pt, $border['r']['color'], $border['r']['type'], $border['r']['width'], $bord);
        }

        // draw the bottom border
        if ($testBb) {
            $pt = array();
            $pt[] = $x+$w;
            $pt[] = $y+$h;
            $pt[] = $x+$w-$border['r']['width'];
            $pt[] = $y+$h;
            $pt[] = $x+$border['l']['width'];
            $pt[] = $y+$h;
            $pt[] = $x;
            $pt[] = $y+$h;
            $pt[] = $x+$border['l']['width'];
            $pt[] = $y+$h-$border['b']['width'];
            $pt[] = $x+$w-$border['r']['width'];
            $pt[] = $y+$h-$border['b']['width'];

            $bord = 3;
            if (is_array($outBL)) {
                $bord-=2;
                $pt[4] += $outBL[0] - $border['l']['width'];
                if ($inBL) {
                    $pt[8] += $inBL[0];
                }
                unset($pt[6]);
                unset($pt[7]);
            }
            if (is_array($outBR)) {
                $bord-=1;
                $pt[2] -= $outBR[0] - $border['r']['width'];
                if ($inBR) {
                    $pt[10]-= $inBR[0];
                }
                unset($pt[0]);
                unset($pt[1]);

            }

            $pt = array_values($pt);
            $this->_drawLine($pt, $border['b']['color'], $border['b']['type'], $border['b']['width'], $bord);
        }

        if ($background['color']) {
            $this->pdf->SetFillColorArray($background['color']);
        }

        return true;
    }

    /**
     * draw a curve (for border radius)
     *
     * @access protected
     * @param  array $pt
     * @param  array $color
     */
    protected function _drawCurve($pt, $color)
    {
        $this->pdf->SetFillColorArray($color);

        if (count($pt) == 10) {
            $this->pdf->drawCurve($pt[0], $pt[1], $pt[2], $pt[3], $pt[4], $pt[5], $pt[6], $pt[7], $pt[8], $pt[9]);
        } else {
            $this->pdf->drawCorner($pt[0], $pt[1], $pt[2], $pt[3], $pt[4], $pt[5], $pt[6], $pt[7]);
        }
    }

    /**
     * draw a line with a specific type, and specific start and end for radius
     *
     * @access protected
     * @param  array   $pt
     * @param  array   $color
     * @param  string  $type (dashed, dotted, double, solid)
     * @param  float   $width
     * @param  integer $radius (binary from 0 to 3 with 1=>start with a radius, 2=>end with a radius)
     */
    protected function _drawLine($pt, $color, $type, $width, $radius = 3)
    {
        // set the fill color
        $this->pdf->SetFillColorArray($color);

        // if dashed or dotted
        if ($type === 'dashed' || $type === 'dotted') {

            // clean the end of the line, if radius
            if ($radius == 1) {
                $tmp = array();
                $tmp[]=$pt[0];
                $tmp[]=$pt[1];
                $tmp[]=$pt[2];
                $tmp[]=$pt[3];
                $tmp[]=$pt[8];
                $tmp[]=$pt[9];
                $this->pdf->Polygon($tmp, 'F');

                $tmp = array();
                $tmp[]=$pt[2];
                $tmp[]=$pt[3];
                $tmp[]=$pt[4];
                $tmp[]=$pt[5];
                $tmp[]=$pt[6];
                $tmp[]=$pt[7];
                $tmp[]=$pt[8];
                $tmp[]=$pt[9];
                $pt = $tmp;
            } elseif ($radius == 2) {
                $tmp = array();
                $tmp[]=$pt[2];
                $tmp[]=$pt[3];
                $tmp[]=$pt[4];
                $tmp[]=$pt[5];
                $tmp[]=$pt[6];
                $tmp[]=$pt[7];
                $this->pdf->Polygon($tmp, 'F');

                $tmp = array();
                $tmp[]=$pt[0];
                $tmp[]=$pt[1];
                $tmp[]=$pt[2];
                $tmp[]=$pt[3];
                $tmp[]=$pt[6];
                $tmp[]=$pt[7];
                $tmp[]=$pt[8];
                $tmp[]=$pt[9];
                $pt = $tmp;
            } elseif ($radius == 3) {
                $tmp = array();
                $tmp[]=$pt[0];
                $tmp[]=$pt[1];
                $tmp[]=$pt[2];
                $tmp[]=$pt[3];
                $tmp[]=$pt[10];
                $tmp[]=$pt[11];
                $this->pdf->Polygon($tmp, 'F');

                $tmp = array();
                $tmp[]=$pt[4];
                $tmp[]=$pt[5];
                $tmp[]=$pt[6];
                $tmp[]=$pt[7];
                $tmp[]=$pt[8];
                $tmp[]=$pt[9];
                $this->pdf->Polygon($tmp, 'F');

                $tmp = array();
                $tmp[]=$pt[2];
                $tmp[]=$pt[3];
                $tmp[]=$pt[4];
                $tmp[]=$pt[5];
                $tmp[]=$pt[8];
                $tmp[]=$pt[9];
                $tmp[]=$pt[10];
                $tmp[]=$pt[11];
                $pt = $tmp;
            }

            // horisontal or vertical line
            if ($pt[2] == $pt[0]) {
                $l = abs(($pt[3]-$pt[1])*0.5);
                $px = 0;
                $py = $width;
                $x1 = $pt[0];
                $y1 = ($pt[3]+$pt[1])*0.5;
                $x2 = $pt[6];
                $y2 = ($pt[7]+$pt[5])*0.5;
            } else {
                $l = abs(($pt[2]-$pt[0])*0.5);
                $px = $width;
                $py = 0;
                $x1 = ($pt[2]+$pt[0])*0.5;
                $y1 = $pt[1];
                $x2 = ($pt[6]+$pt[4])*0.5;
                $y2 = $pt[7];
            }

            // if dashed : 3x bigger than dotted
            if ($type === 'dashed') {
                $px = $px*3.;
                $py = $py*3.;
            }
            $mode = ($l/($px+$py)<.5);

            // display the dotted/dashed line
            for ($i=0; $l-($px+$py)*($i-0.5)>0; $i++) {
                if (($i%2) == $mode) {
                    $j = $i-0.5;
                    $lx1 = $px*$j;
                    if ($lx1<-$l) {
                        $lx1 =-$l;
                    }
                    $ly1 = $py*$j;
                    if ($ly1<-$l) {
                        $ly1 =-$l;
                    }
                    $lx2 = $px*($j+1);
                    if ($lx2>$l) {
                        $lx2 = $l;
                    }
                    $ly2 = $py*($j+1);
                    if ($ly2>$l) {
                        $ly2 = $l;
                    }

                    $tmp = array();
                    $tmp[] = $x1+$lx1;
                    $tmp[] = $y1+$ly1;
                    $tmp[] = $x1+$lx2;
                    $tmp[] = $y1+$ly2;
                    $tmp[] = $x2+$lx2;
                    $tmp[] = $y2+$ly2;
                    $tmp[] = $x2+$lx1;
                    $tmp[] = $y2+$ly1;
                    $this->pdf->Polygon($tmp, 'F');

                    if ($j>0) {
                        $tmp = array();
                        $tmp[] = $x1-$lx1;
                        $tmp[] = $y1-$ly1;
                        $tmp[] = $x1-$lx2;
                        $tmp[] = $y1-$ly2;
                        $tmp[] = $x2-$lx2;
                        $tmp[] = $y2-$ly2;
                        $tmp[] = $x2-$lx1;
                        $tmp[] = $y2-$ly1;
                        $this->pdf->Polygon($tmp, 'F');
                    }
                }
            }
        } elseif ($type === 'double') {

            // if double, 2 lines : 0=>1/3 and 2/3=>1
            $pt1 = $pt;
            $pt2 = $pt;

            if (count($pt) == 12) {
                // line 1
                $pt1[0] = ($pt[0]-$pt[10])*0.33 + $pt[10];
                $pt1[1] = ($pt[1]-$pt[11])*0.33 + $pt[11];
                $pt1[2] = ($pt[2]-$pt[10])*0.33 + $pt[10];
                $pt1[3] = ($pt[3]-$pt[11])*0.33 + $pt[11];
                $pt1[4] = ($pt[4]-$pt[8])*0.33 + $pt[8];
                $pt1[5] = ($pt[5]-$pt[9])*0.33 + $pt[9];
                $pt1[6] = ($pt[6]-$pt[8])*0.33 + $pt[8];
                $pt1[7] = ($pt[7]-$pt[9])*0.33 + $pt[9];
                $pt2[10]= ($pt[10]-$pt[0])*0.33 + $pt[0];
                $pt2[11]= ($pt[11]-$pt[1])*0.33 + $pt[1];

                // line 2
                $pt2[2] = ($pt[2] -$pt[0])*0.33 + $pt[0];
                $pt2[3] = ($pt[3] -$pt[1])*0.33 + $pt[1];
                $pt2[4] = ($pt[4] -$pt[6])*0.33 + $pt[6];
                $pt2[5] = ($pt[5] -$pt[7])*0.33 + $pt[7];
                $pt2[8] = ($pt[8] -$pt[6])*0.33 + $pt[6];
                $pt2[9] = ($pt[9] -$pt[7])*0.33 + $pt[7];
            } else {
                // line 1
                $pt1[0] = ($pt[0]-$pt[6])*0.33 + $pt[6];
                $pt1[1] = ($pt[1]-$pt[7])*0.33 + $pt[7];
                $pt1[2] = ($pt[2]-$pt[4])*0.33 + $pt[4];
                $pt1[3] = ($pt[3]-$pt[5])*0.33 + $pt[5];

                // line 2
                $pt2[6] = ($pt[6]-$pt[0])*0.33 + $pt[0];
                $pt2[7] = ($pt[7]-$pt[1])*0.33 + $pt[1];
                $pt2[4] = ($pt[4]-$pt[2])*0.33 + $pt[2];
                $pt2[5] = ($pt[5]-$pt[3])*0.33 + $pt[3];
            }
            $this->pdf->Polygon($pt1, 'F');
            $this->pdf->Polygon($pt2, 'F');
        } elseif ($type === 'solid') {
            // solid line : draw directly the polygon
            $this->pdf->Polygon($pt, 'F');
        }
    }

    /**
     * @access protected
     * @param  &array $cases
     * @param  &array $corr
     */
    protected function _calculateTableCellSize(&$cases, &$corr)
    {
        if (!isset($corr[0])) {
            return true;
        }

        $amountCorr = count($corr);
        $amountCorr0 = count($corr[0]);

        // for each cell without colspan, we get the max width for each column
        $sw = array();
        for ($x=0; $x<$amountCorr0; $x++) {
            $m=0;
            for ($y=0; $y<$amountCorr; $y++) {
                if (isset($corr[$y][$x]) && is_array($corr[$y][$x]) && $corr[$y][$x][2] == 1) {
                    $m = max($m, $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['w']);
                }
            }
            $sw[$x] = $m;
        }

        // for each cell with colspan, we adapt the width of each column
        for ($x=0; $x<$amountCorr0; $x++) {
            for ($y=0; $y<$amountCorr; $y++) {
                if (isset($corr[$y][$x]) && is_array($corr[$y][$x]) && $corr[$y][$x][2]>1) {

                    // sum the max width of each column in colspan
                    // if  you have an error here, it is because you have not the same number of columns on each row...
                    $s = 0;
                    for ($i=0; $i<$corr[$y][$x][2]; $i++) {
                        $s+= $sw[$x+$i];
                    }

                    // if the max width is < the width of the cell with colspan => we adapt the width of each max width
                    if ($s>0 && $s<$cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['w']) {
                        for ($i=0; $i<$corr[$y][$x][2]; $i++) {
                            $sw[$x+$i] = $sw[$x+$i]/$s*$cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['w'];
                        }
                    }
                }
            }
        }

        // set the new width, for each cell
        for ($x=0; $x<$amountCorr0; $x++) {
            for ($y=0; $y<$amountCorr; $y++) {
                if (isset($corr[$y][$x]) && is_array($corr[$y][$x])) {
                    // without colspan
                    if ($corr[$y][$x][2] == 1) {
                        $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['w'] = $sw[$x];
                    // with colspan
                    } else {
                        $s = 0;
                        for ($i=0; $i<$corr[$y][$x][2]; $i++) {
                            $s+= $sw[$x+$i];
                        }
                        $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['w'] = $s;
                    }
                }
            }
        }

        // for each cell without rowspan, we get the max height for each line
        $sh = array();
        for ($y=0; $y<$amountCorr; $y++) {
            $m=0;
            for ($x=0; $x<$amountCorr0; $x++) {
                if (isset($corr[$y][$x]) && is_array($corr[$y][$x]) && $corr[$y][$x][3] == 1) {
                    $m = max($m, $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['h']);
                }
            }
            $sh[$y] = $m;
        }

        // for each cell with rowspan, we adapt the height of each line
        for ($y=0; $y<$amountCorr; $y++) {
            for ($x=0; $x<$amountCorr0; $x++) {
                if (isset($corr[$y][$x]) && is_array($corr[$y][$x]) && $corr[$y][$x][3]>1) {

                    // sum the max height of each line in rowspan
                    $s = 0;
                    for ($i=0; $i<$corr[$y][$x][3]; $i++) {
                        $s+= isset($sh[$y+$i]) ? $sh[$y+$i] : 0;
                    }

                    // if the max height is < the height of the cell with rowspan => we adapt the height of each max height
                    if ($s>0 && $s<$cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['h']) {
                        for ($i=0; $i<$corr[$y][$x][3]; $i++) {
                            $sh[$y+$i] = $sh[$y+$i]/$s*$cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['h'];
                        }
                    }
                }
            }
        }

        // set the new height, for each cell
        for ($y=0; $y<$amountCorr; $y++) {
            for ($x=0; $x<$amountCorr0; $x++) {
                if (isset($corr[$y][$x]) && is_array($corr[$y][$x])) {
                    // without rowspan
                    if ($corr[$y][$x][3] == 1) {
                        $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['h'] = $sh[$y];
                    // with rowspan
                    } else {
                        $s = 0;
                        for ($i=0; $i<$corr[$y][$x][3]; $i++) {
                            $s+= $sh[$y+$i];
                        }
                        $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['h'] = $s;

                        for ($j=1; $j<$corr[$y][$x][3]; $j++) {
                            $tx = $x+1;
                            $ty = $y+$j;
                            for (true; isset($corr[$ty][$tx]) && !is_array($corr[$ty][$tx]);
                            $tx++) {

                            }
                            if (isset($corr[$ty][$tx])) {
                                $cases[$corr[$ty][$tx][1]][$corr[$ty][$tx][0]]['dw']+= $cases[$corr[$y][$x][1]][$corr[$y][$x][0]]['w'];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * tag : PAGE
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_PAGE($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }
        if (!is_null($this->debug)) {
            $this->debug->addStep('PAGE '.($this->_page+1), true);
        }

        $newPageSet= (!isset($param['pageset']) || $param['pageset'] !== 'old');

        $resetPageNumber = (isset($param['pagegroup']) && $param['pagegroup'] === 'new');

        if (array_key_exists('hideheader', $param) && $param['hideheader'] !== 'false' && !empty($param['hideheader'])) {
            $this->_hideHeader = (array) array_merge($this->_hideHeader, explode(',', $param['hideheader']));
        }

        if (array_key_exists('hidefooter', $param) && $param['hidefooter'] !== 'false' && !empty($param['hidefooter'])) {
            $this->_hideFooter = (array) array_merge($this->_hideFooter, explode(',', $param['hidefooter']));
        }

        $this->_maxH = 0;

        // if new page set asked
        if ($newPageSet) {
            $this->_subHEADER = array();
            $this->_subFOOTER = array();

            // orientation
            $orientation = '';
            if (isset($param['orientation'])) {
                $param['orientation'] = strtolower($param['orientation']);
                if ($param['orientation'] === 'p') {
                    $orientation = 'P';
                }
                if ($param['orientation'] === 'portrait') {
                    $orientation = 'P';
                }

                if ($param['orientation'] === 'l') {
                    $orientation = 'L';
                }
                if ($param['orientation'] === 'paysage') {
                    $orientation = 'L';
                }
                if ($param['orientation'] === 'landscape') {
                    $orientation = 'L';
                }
            }

            // format
            $format = null;
            if (isset($param['format'])) {
                $format = (string) $param['format'];
                if (preg_match('/^([0-9]+)x([0-9]+)$/isU', $format, $match)) {
                    $format = array((int)$match[1], (int)$match[2]);
                }
            }

            // background
            $background = array();
            if (isset($param['backimg'])) {
                $background['img']    = isset($param['backimg'])  ? $param['backimg']  : '';       // src of the image
                $background['posX']   = isset($param['backimgx']) ? $param['backimgx'] : 'center'; // horizontal position of the image
                $background['posY']   = isset($param['backimgy']) ? $param['backimgy'] : 'middle'; // vertical position of the image
                $background['width']  = isset($param['backimgw']) ? $param['backimgw'] : '100%';   // width of the image (100% = page width)

                // convert the src of the image, if parameters
                $background['img'] = str_replace('&amp;', '&', $background['img']);

                // convert the positions
                if ($background['posX'] === 'left') {
                    $background['posX'] = '0%';
                }
                if ($background['posX'] === 'center') {
                    $background['posX'] = '50%';
                }
                if ($background['posX'] === 'right') {
                    $background['posX'] = '100%';
                }
                if ($background['posY'] === 'top') {
                    $background['posY'] = '0%';
                }
                if ($background['posY'] === 'middle') {
                    $background['posY'] = '50%';
                }
                if ($background['posY'] === 'bottom') {
                    $background['posY'] = '100%';
                }

                if ($background['img']) {
                    // get the size of the image
                    // WARNING : if URL, "allow_url_fopen" must turned to "on" in php.ini
                    $infos=@getimagesize($background['img']);
                    if (is_array($infos) && count($infos)>1) {
                        $imageWidth = $this->cssConverter->convertToMM($background['width'], $this->pdf->getW());
                        $imageHeight = $imageWidth*$infos[1]/$infos[0];

                        $background['width'] = $imageWidth;
                        $background['posX']  = $this->cssConverter->convertToMM($background['posX'], $this->pdf->getW() - $imageWidth);
                        $background['posY']  = $this->cssConverter->convertToMM($background['posY'], $this->pdf->getH() - $imageHeight);
                    } else {
                        $background = array();
                    }
                } else {
                    $background = array();
                }
            }

            // margins of the page
            $background['top']    = isset($param['backtop'])    ? $param['backtop']    : '0';
            $background['bottom'] = isset($param['backbottom']) ? $param['backbottom'] : '0';
            $background['left']   = isset($param['backleft'])   ? $param['backleft']   : '0';
            $background['right']  = isset($param['backright'])  ? $param['backright']  : '0';

            // if no unit => mm
            if (preg_match('/^([0-9]*)$/isU', $background['top'])) {
                $background['top']    .= 'mm';
            }
            if (preg_match('/^([0-9]*)$/isU', $background['bottom'])) {
                $background['bottom'] .= 'mm';
            }
            if (preg_match('/^([0-9]*)$/isU', $background['left'])) {
                $background['left']   .= 'mm';
            }
            if (preg_match('/^([0-9]*)$/isU', $background['right'])) {
                $background['right']  .= 'mm';
            }

            // convert to mm
            $background['top']    = $this->cssConverter->convertToMM($background['top'], $this->pdf->getH());
            $background['bottom'] = $this->cssConverter->convertToMM($background['bottom'], $this->pdf->getH());
            $background['left']   = $this->cssConverter->convertToMM($background['left'], $this->pdf->getW());
            $background['right']  = $this->cssConverter->convertToMM($background['right'], $this->pdf->getW());

            // get the background color
            $res = false;
            $background['color'] = isset($param['backcolor']) ? $this->cssConverter->convertToColor($param['backcolor'], $res) : null;
            if (!$res) {
                $background['color'] = null;
            }

            $this->parsingCss->save();
            $this->parsingCss->analyse('PAGE', $param);
            $this->parsingCss->setPosition();
            $this->parsingCss->fontSet();

            // new page
            $this->_setNewPage($format, $orientation, $background, null, $resetPageNumber);

            // automatic footer
            if (isset($param['footer'])) {
                $lst = explode(';', $param['footer']);
                foreach ($lst as $key => $val) {
                    $lst[$key] = trim(strtolower($val));
                }
                $page    = in_array('page', $lst);
                $date    = in_array('date', $lst);
                $time    = in_array('time', $lst);
                $form    = in_array('form', $lst);
            } else {
                $page    = null;
                $date    = null;
                $time    = null;
                $form    = null;
            }
            $this->pdf->SetMyFooter($page, $date, $time, $form);
        // else => we use the last page set used
        } else {
            $this->parsingCss->save();
            $this->parsingCss->analyse('PAGE', $param);
            $this->parsingCss->setPosition();
            $this->parsingCss->fontSet();

            $this->_setNewPage(null, null, null, null, $resetPageNumber);
        }

        return true;
    }

    /**
     * tag : PAGE
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_PAGE($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        if (!is_null($this->debug)) {
            $this->debug->addStep('PAGE '.$this->_page, false);
        }

        return true;
    }

    /**
     * tag : PAGE_HEADER
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_PAGE_HEADER($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $amountHtmlCodes = count($this->parsingHtml->code);

        $this->_subHEADER = array();
        for ($this->_parsePos; $this->_parsePos<$amountHtmlCodes; $this->_parsePos++) {
            $action = $this->parsingHtml->code[$this->_parsePos];
            if ($action->getName() === 'page_header') {
                $action->setName('page_header_sub');
            }
            $this->_subHEADER[] = $action;
            if (strtolower($action->getName()) === 'page_header_sub' && $action->isClose()) {
                break;
            }
        }

        $this->_setPageHeader();

        return true;
    }

    /**
     * tag : PAGE_FOOTER
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_PAGE_FOOTER($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $amountHtmlCodes = count($this->parsingHtml->code);

        $this->_subFOOTER = array();
        for ($this->_parsePos; $this->_parsePos<$amountHtmlCodes; $this->_parsePos++) {
            $action = $this->parsingHtml->code[$this->_parsePos];
            if ($action->getName() === 'page_footer') {
                $action->setName('page_footer_sub');
            }
            $this->_subFOOTER[] = $action;
            if (strtolower($action->getName()) === 'page_footer_sub' && $action->isClose()) {
                break;
            }
        }

        $this->_setPageFooter();

        return true;
    }

    /**
     * It is not a real tag. Does not use it directly
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_PAGE_HEADER_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        // save the current stat
        $this->_subSTATES = array();
        $this->_subSTATES['x']  = $this->pdf->GetX();
        $this->_subSTATES['y']  = $this->pdf->GetY();
        $this->_subSTATES['s']  = $this->parsingCss->value;
        $this->_subSTATES['t']  = $this->parsingCss->table;
        $this->_subSTATES['ml'] = $this->_margeLeft;
        $this->_subSTATES['mr'] = $this->_margeRight;
        $this->_subSTATES['mt'] = $this->_margeTop;
        $this->_subSTATES['mb'] = $this->_margeBottom;
        $this->_subSTATES['mp'] = $this->_pageMarges;

        // new stat for the header
        $this->_pageMarges = array();
        $this->_margeLeft    = $this->_defaultLeft;
        $this->_margeRight   = $this->_defaultRight;
        $this->_margeTop     = $this->_defaultTop;
        $this->_margeBottom  = $this->_defaultBottom;
        $this->pdf->SetMargins($this->_margeLeft, $this->_margeTop, $this->_margeRight);
        $this->pdf->SetAutoPageBreak(false, $this->_margeBottom);
        $this->pdf->SetXY($this->_defaultLeft, $this->_defaultTop);

        $this->parsingCss->initStyle();
        $this->parsingCss->resetStyle();
        $this->parsingCss->value['width'] = $this->pdf->getW() - $this->_defaultLeft - $this->_defaultRight;
        $this->parsingCss->table = array();

        $this->parsingCss->save();
        $this->parsingCss->analyse('page_header_sub', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();
        $this->_setNewPositionForNewLine();
        return true;
    }

    /**
     * It is not a real tag. Does not use it directly
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_PAGE_HEADER_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();

        // restore the stat
        $this->parsingCss->value = $this->_subSTATES['s'];
        $this->parsingCss->table = $this->_subSTATES['t'];
        $this->_pageMarges       = $this->_subSTATES['mp'];
        $this->_margeLeft        = $this->_subSTATES['ml'];
        $this->_margeRight       = $this->_subSTATES['mr'];
        $this->_margeTop         = $this->_subSTATES['mt'];
        $this->_margeBottom      = $this->_subSTATES['mb'];
        $this->pdf->SetMargins($this->_margeLeft, $this->_margeTop, $this->_margeRight);
        $this->pdf->setbMargin($this->_margeBottom);
        $this->pdf->SetAutoPageBreak(false, $this->_margeBottom);
        $this->pdf->SetXY($this->_subSTATES['x'], $this->_subSTATES['y']);

        $this->parsingCss->fontSet();
        $this->_maxH = 0;

        return true;
    }

    /**
     * It is not a real tag. Does not use it directly
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_PAGE_FOOTER_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        // save the current stat
        $this->_subSTATES = array();
        $this->_subSTATES['x']    = $this->pdf->GetX();
        $this->_subSTATES['y']    = $this->pdf->GetY();
        $this->_subSTATES['s']    = $this->parsingCss->value;
        $this->_subSTATES['t']    = $this->parsingCss->table;
        $this->_subSTATES['ml']    = $this->_margeLeft;
        $this->_subSTATES['mr']    = $this->_margeRight;
        $this->_subSTATES['mt']    = $this->_margeTop;
        $this->_subSTATES['mb']    = $this->_margeBottom;
        $this->_subSTATES['mp']    = $this->_pageMarges;

        // new stat for the footer
        $this->_pageMarges  = array();
        $this->_margeLeft   = $this->_defaultLeft;
        $this->_margeRight  = $this->_defaultRight;
        $this->_margeTop    = $this->_defaultTop;
        $this->_margeBottom = $this->_defaultBottom;
        $this->pdf->SetMargins($this->_margeLeft, $this->_margeTop, $this->_margeRight);
        $this->pdf->SetAutoPageBreak(false, $this->_margeBottom);
        $this->pdf->SetXY($this->_defaultLeft, $this->_defaultTop);

        $this->parsingCss->initStyle();
        $this->parsingCss->resetStyle();
        $this->parsingCss->value['width']    = $this->pdf->getW() - $this->_defaultLeft - $this->_defaultRight;
        $this->parsingCss->table                = array();

        // we create a sub HTML2PFDF, and we execute on it the content of the footer, to get the height of it
        $sub = $this->createSubHTML();
        $sub->parsingHtml->code = $this->parsingHtml->getLevel($this->_parsePos);
        $sub->_makeHTMLcode();
        $this->pdf->SetY($this->pdf->getH() - $sub->_maxY - $this->_defaultBottom - 0.01);
        $this->_destroySubHTML($sub);

        $this->parsingCss->save();
        $this->parsingCss->analyse('page_footer_sub', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();
        $this->_setNewPositionForNewLine();

        return true;
    }

    /**
     * It is not a real tag. Do not use it directly
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_PAGE_FOOTER_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();

        $this->parsingCss->value                = $this->_subSTATES['s'];
        $this->parsingCss->table                = $this->_subSTATES['t'];
        $this->_pageMarges                 = $this->_subSTATES['mp'];
        $this->_margeLeft                = $this->_subSTATES['ml'];
        $this->_margeRight                = $this->_subSTATES['mr'];
        $this->_margeTop                    = $this->_subSTATES['mt'];
        $this->_margeBottom                = $this->_subSTATES['mb'];
        $this->pdf->SetMargins($this->_margeLeft, $this->_margeTop, $this->_margeRight);
        $this->pdf->SetAutoPageBreak(false, $this->_margeBottom);
        $this->pdf->SetXY($this->_subSTATES['x'], $this->_subSTATES['y']);

        $this->parsingCss->fontSet();
        $this->_maxH = 0;

        return true;
    }

    /**
     * tag : NOBREAK
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_NOBREAK($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        // create a sub Html2Pdf to execute the content of the tag, to get the dimensions
        $sub = $this->createSubHTML();
        $sub->parsingHtml->code = $this->parsingHtml->getLevel($this->_parsePos);
        $sub->_makeHTMLcode();
        $y = $this->pdf->GetY();

        // if the content does not fit on the page => new page
        if ($sub->_maxY < ($this->pdf->getH() - $this->pdf->gettMargin()-$this->pdf->getbMargin()) &&
            $y + $sub->_maxY>=($this->pdf->getH() - $this->pdf->getbMargin())
        ) {
            $this->_setNewPage();
        }

        // destroy the sub Html2Pdf
        $this->_destroySubHTML($sub);

        return true;
    }


    /**
     * tag : NOBREAK
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_NOBREAK($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        return true;
    }

    /**
     * tag : DIV
     * mode : OPEN
     *
     * @param  array $param
     * @param  string $other name of tag that used the div tag
     * @return boolean
     */
    protected function _tag_open_DIV($param, $other = 'div')
    {
        if ($this->_isForOneLine) {
            return false;
        }

        if (!is_null($this->debug)) {
            $this->debug->addStep(strtoupper($other), true);
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse($other, $param);
        $this->parsingCss->fontSet();

        // for fieldset and legend
        if (in_array($other, array('fieldset', 'legend'))) {
            if (isset($param['moveTop'])) {
                $this->parsingCss->value['margin']['t']    += $param['moveTop'];
            }
            if (isset($param['moveLeft'])) {
                $this->parsingCss->value['margin']['l']    += $param['moveLeft'];
            }
            if (isset($param['moveDown'])) {
                $this->parsingCss->value['margin']['b']    += $param['moveDown'];
            }
        }

        $alignObject = null;
        if ($this->parsingCss->value['margin-auto']) {
            $alignObject = 'center';
        }

        $marge = array();
        $marge['l'] = $this->parsingCss->value['border']['l']['width'] + $this->parsingCss->value['padding']['l']+0.03;
        $marge['r'] = $this->parsingCss->value['border']['r']['width'] + $this->parsingCss->value['padding']['r']+0.03;
        $marge['t'] = $this->parsingCss->value['border']['t']['width'] + $this->parsingCss->value['padding']['t']+0.03;
        $marge['b'] = $this->parsingCss->value['border']['b']['width'] + $this->parsingCss->value['padding']['b']+0.03;

        // extract the content of the div
        $level = $this->parsingHtml->getLevel($this->_parsePos);

        // create a sub Html2Pdf to get the dimensions of the content of the div
        $w = 0;
        $h = 0;
        if (count($level)) {
            $sub = $this->createSubHTML();
            $sub->parsingHtml->code = $level;
            $sub->_makeHTMLcode();
            $w = $sub->_maxX;
            $h = $sub->_maxY;
            $this->_destroySubHTML($sub);
        }
        $wReel = $w;
        $hReel = $h;

        $w+= $marge['l']+$marge['r']+0.001;
        $h+= $marge['t']+$marge['b']+0.001;

        if ($this->parsingCss->value['overflow'] === 'hidden') {
            $overW = max($w, $this->parsingCss->value['width']);
            $overH = max($h, $this->parsingCss->value['height']);
            $overflow = true;
            $this->parsingCss->value['old_maxX'] = $this->_maxX;
            $this->parsingCss->value['old_maxY'] = $this->_maxY;
            $this->parsingCss->value['old_maxH'] = $this->_maxH;
            $this->parsingCss->value['old_overflow'] = $this->_isInOverflow;
            $this->_isInOverflow = true;
        } else {
            $overW = null;
            $overH = null;
            $overflow = false;
            $this->parsingCss->value['width']  = max($w, $this->parsingCss->value['width']);
            $this->parsingCss->value['height'] = max($h, $this->parsingCss->value['height']);
        }

        switch ($this->parsingCss->value['rotate']) {
            case 90:
                $tmp = $overH;
                $overH = $overW;
                $overW = $tmp;
                $tmp = $hReel;
                $hReel = $wReel;
                $wReel = $tmp;
                unset($tmp);
                $w = $this->parsingCss->value['height'];
                $h = $this->parsingCss->value['width'];
                $tX =-$h;
                $tY = 0;
                break;

            case 180:
                $w = $this->parsingCss->value['width'];
                $h = $this->parsingCss->value['height'];
                $tX = -$w;
                $tY = -$h;
                break;

            case 270:
                $tmp = $overH;
                $overH = $overW;
                $overW = $tmp;
                $tmp = $hReel;
                $hReel = $wReel;
                $wReel = $tmp;
                unset($tmp);
                $w = $this->parsingCss->value['height'];
                $h = $this->parsingCss->value['width'];
                $tX = 0;
                $tY =-$w;
                break;

            default:
                $w = $this->parsingCss->value['width'];
                $h = $this->parsingCss->value['height'];
                $tX = 0;
                $tY = 0;
                break;
        }

        $maxW = ($this->pdf->getW() - $this->pdf->getlMargin()-$this->pdf->getrMargin());
        $maxH = ($this->pdf->getH() - $this->pdf->gettMargin()-$this->pdf->getbMargin());
        $maxX = ($this->pdf->getW() - $this->pdf->getrMargin());
        $maxY = ($this->pdf->getH() - $this->pdf->getbMargin());
        $endX = ($this->pdf->GetX() + $w);
        $endY = ($this->pdf->GetY() + $h);

        $w = round($w, 6);
        $h = round($h, 6);
        $maxW = round($maxW, 6);
        $maxH = round($maxH, 6);
        $maxX = round($maxX, 6);
        $maxY = round($maxY, 6);
        $endX = round($endX, 6);
        $endY = round($endY, 6);

        if ($this->parsingCss->value['page-break-before'] == "always") {
            $this->_setNewPage();
        }

        if (!$this->parsingCss->value['position']) {
            if ($w < $maxW && $endX >= $maxX) {
                $this->_tag_open_BR(array());
            }

            if ($h < $maxH && $endY >= $maxY && !$this->_isInOverflow) {
                $this->_setNewPage();
            }

            $old = $this->parsingCss->getOldValues();
            $parentWidth = $old['width'] ? $old['width'] : $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();

            if ($parentWidth>$w) {
                if ($alignObject === 'center') {
                    $this->pdf->SetX($this->pdf->GetX() + ($parentWidth-$w)*0.5);
                } elseif ($alignObject === 'right') {
                    $this->pdf->SetX($this->pdf->GetX() + $parentWidth-$w);
                }
            }

            $this->parsingCss->setPosition();
        } else {
            $old = $this->parsingCss->getOldValues();
            $parentWidth = $old['width'] ? $old['width'] : $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();

            if ($parentWidth>$w) {
                if ($alignObject === 'center') {
                    $this->pdf->SetX($this->pdf->GetX() + ($parentWidth-$w)*0.5);
                } elseif ($alignObject === 'right') {
                    $this->pdf->SetX($this->pdf->GetX() + $parentWidth-$w);
                }
            }

            $this->parsingCss->setPosition();
            $this->_saveMax();
            $this->_maxX = 0;
            $this->_maxY = 0;
            $this->_maxH = 0;
            $this->_maxE = 0;
        }

        if ($this->parsingCss->value['rotate']) {
            $this->pdf->startTransform();
            $this->pdf->setRotation($this->parsingCss->value['rotate']);
            $this->pdf->setTranslate($tX, $tY);
        }

        $this->_drawRectangle(
            $this->parsingCss->value['x'],
            $this->parsingCss->value['y'],
            $this->parsingCss->value['width'],
            $this->parsingCss->value['height'],
            $this->parsingCss->value['border'],
            $this->parsingCss->value['padding'],
            0,
            $this->parsingCss->value['background']
        );

        $marge = array();
        $marge['l'] = $this->parsingCss->value['border']['l']['width'] + $this->parsingCss->value['padding']['l']+0.03;
        $marge['r'] = $this->parsingCss->value['border']['r']['width'] + $this->parsingCss->value['padding']['r']+0.03;
        $marge['t'] = $this->parsingCss->value['border']['t']['width'] + $this->parsingCss->value['padding']['t']+0.03;
        $marge['b'] = $this->parsingCss->value['border']['b']['width'] + $this->parsingCss->value['padding']['b']+0.03;

        $this->parsingCss->value['width'] -= $marge['l']+$marge['r'];
        $this->parsingCss->value['height']-= $marge['t']+$marge['b'];

        $xCorr = 0;
        $yCorr = 0;
        if (!$this->_subPart && !$this->_isSubPart) {
            switch ($this->parsingCss->value['text-align']) {
                case 'right':
                    $xCorr = ($this->parsingCss->value['width']-$wReel);
                    break;
                case 'center':
                    $xCorr = ($this->parsingCss->value['width']-$wReel)*0.5;
                    break;
            }
            if ($xCorr>0) {
                $xCorr=0;
            }
            switch ($this->parsingCss->value['vertical-align']) {
                case 'bottom':
                    $yCorr = ($this->parsingCss->value['height']-$hReel);
                    break;
                case 'middle':
                    $yCorr = ($this->parsingCss->value['height']-$hReel)*0.5;
                    break;
            }
        }

        if ($overflow) {
            $overW-= $marge['l']+$marge['r'];
            $overH-= $marge['t']+$marge['b'];
            $this->pdf->clippingPathStart(
                $this->parsingCss->value['x']+$marge['l'],
                $this->parsingCss->value['y']+$marge['t'],
                $this->parsingCss->value['width'],
                $this->parsingCss->value['height']
            );

            $this->parsingCss->value['x']+= $xCorr;

            // marges from the dimension of the content
            $mL = $this->parsingCss->value['x']+$marge['l'];
            $mR = $this->pdf->getW() - $mL - $overW;
        } else {
            // marges from the dimension of the div
            $mL = $this->parsingCss->value['x']+$marge['l'];
            $mR = $this->pdf->getW() - $mL - $this->parsingCss->value['width'];
        }

        $x = $this->parsingCss->value['x']+$marge['l'];
        $y = $this->parsingCss->value['y']+$marge['t']+$yCorr;
        $this->_saveMargin($mL, 0, $mR);
        $this->pdf->SetXY($x, $y);

        $this->_setNewPositionForNewLine();

        return true;
    }

    /**
     * tag : BLOCKQUOTE
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_BLOCKQUOTE($param)
    {
        return $this->_tag_open_DIV($param, 'blockquote');
    }

    /**
     * tag : LEGEND
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_LEGEND($param)
    {
        return $this->_tag_open_DIV($param, 'legend');
    }

    /**
     * tag : FIELDSET
     * mode : OPEN
     *
     * @author Pavel Kochman
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_FIELDSET($param)
    {

        $this->parsingCss->save();
        $this->parsingCss->analyse('fieldset', $param);

        $amountHtmlCodes = count($this->parsingHtml->code);

        // get height of LEGEND element and make fieldset corrections
        for ($tempPos = $this->_parsePos + 1; $tempPos<$amountHtmlCodes; $tempPos++) {
            $action = $this->parsingHtml->code[$tempPos];
            if ($action->getName() === 'fieldset') {
                break;
            }
            if ($action->getName() === 'legend' && !$action->isClose()) {
                $legendOpenPos = $tempPos;

                $sub = $this->createSubHTML();
                $sub->parsingHtml->code = $this->parsingHtml->getLevel($tempPos - 1);

                $amountSubHtmlCodes = count($sub->parsingHtml->code);
                $res = null;
                for ($sub->_parsePos = 0; $sub->_parsePos<$amountSubHtmlCodes; $sub->_parsePos++) {
                    $action = $sub->parsingHtml->code[$sub->_parsePos];
                    $sub->_executeAction($action);

                    if ($action->getName() === 'legend' && $action->isClose()) {
                        break;
                    }
                }

                $legendH = $sub->_maxY;
                $this->_destroySubHTML($sub);

                $move = $this->parsingCss->value['padding']['t'] + $this->parsingCss->value['border']['t']['width'] + 0.03;

                $param['moveTop'] = $legendH / 2;

                $node = $this->parsingHtml->code[$legendOpenPos];
                $node->setParam('moveTop', - ($legendH / 2 + $move));
                $node->setParam('moveLeft', 2 - $this->parsingCss->value['border']['l']['width'] - $this->parsingCss->value['padding']['l']);
                $node->setParam('moveDown', $move);
                break;
            }
        }
        $this->parsingCss->load();

        return $this->_tag_open_DIV($param, 'fieldset');
    }

    /**
     * tag : DIV
     * mode : CLOSE
     *
     * @param  array $param
     * @param  string $other name of tag that used the div tag
     * @return boolean
     */
    protected function _tag_close_DIV($param, $other = 'div')
    {
        if ($this->_isForOneLine) {
            return false;
        }

        if ($this->parsingCss->value['overflow'] === 'hidden') {
            $this->_maxX = $this->parsingCss->value['old_maxX'];
            $this->_maxY = $this->parsingCss->value['old_maxY'];
            $this->_maxH = $this->parsingCss->value['old_maxH'];
            $this->_isInOverflow = $this->parsingCss->value['old_overflow'];
            $this->pdf->clippingPathStop();
        }

        if ($this->parsingCss->value['rotate']) {
            $this->pdf->stopTransform();
        }

        $marge = array();
        $marge['l'] = $this->parsingCss->value['border']['l']['width'] + $this->parsingCss->value['padding']['l']+0.03;
        $marge['r'] = $this->parsingCss->value['border']['r']['width'] + $this->parsingCss->value['padding']['r']+0.03;
        $marge['t'] = $this->parsingCss->value['border']['t']['width'] + $this->parsingCss->value['padding']['t']+0.03;
        $marge['b'] = $this->parsingCss->value['border']['b']['width'] + $this->parsingCss->value['padding']['b']+0.03;

        $x = $this->parsingCss->value['x'];
        $y = $this->parsingCss->value['y'];
        $w = $this->parsingCss->value['width']+$marge['l']+$marge['r']+$this->parsingCss->value['margin']['r'];
        $h = $this->parsingCss->value['height']+$marge['t']+$marge['b']+$this->parsingCss->value['margin']['b'];

        switch ($this->parsingCss->value['rotate']) {
            case 90:
                $t = $w;
                $w = $h;
                $h = $t;
                break;

            case 270:
                $t = $w;
                $w = $h;
                $h = $t;
                break;

            default:
                break;
        }


        if ($this->parsingCss->value['position'] !== 'absolute') {
            $this->pdf->SetXY($x+$w, $y);

            $this->_maxX = max($this->_maxX, $x+$w);
            $this->_maxY = max($this->_maxY, $y+$h);
            $this->_maxH = max($this->_maxH, $h);
        } else {
            $this->pdf->SetXY($this->parsingCss->value['xc'], $this->parsingCss->value['yc']);

            $this->_loadMax();
        }

        $newLineAfter = ($this->parsingCss->value['display'] !== 'inline' && $this->parsingCss->value['position'] !== 'absolute');
        $newPageAfter = ($this->parsingCss->value['page-break-after'] == "always");

        $this->parsingCss->load();
        $this->parsingCss->fontSet();
        $this->_loadMargin();

        if ($newPageAfter) {
            $this->_setNewPage();
        } elseif ($newLineAfter) {
            $this->_tag_open_BR(array());
        }

        if (!is_null($this->debug)) {
            $this->debug->addStep(strtoupper($other), false);
        }

        return true;
    }

    /**
     * tag : BLOCKQUOTE
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_BLOCKQUOTE($param)
    {
        return $this->_tag_close_DIV($param, 'blockquote');
    }

    /**
     * tag : FIELDSET
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_FIELDSET($param)
    {
        return $this->_tag_close_DIV($param, 'fieldset');
    }

    /**
     * tag : LEGEND
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_LEGEND($param)
    {
        return $this->_tag_close_DIV($param, 'legend');
    }

    /**
     * tag : BARCODE
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_BARCODE($param)
    {
        if (!isset($param['type'])) {
            $param['type'] = 'C39';
        }
        if (!isset($param['value'])) {
            $param['value']    = 0;
        }
        if (!isset($param['label'])) {
            $param['label']    = 'label';
        }
        if (!isset($param['style']['color'])) {
            $param['style']['color'] = '#000000';
        }

        $param['type'] = strtoupper($param['type']);
        if (!isset($param['dimension'])) {
            $param['dimension'] = '1D';
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('barcode', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $w = $this->parsingCss->value['width'];
        if (!$w) {
            $w = $this->cssConverter->convertToMM('50mm');
        }
        $h = $this->parsingCss->value['height'];
        if (!$h) {
            $h = $this->cssConverter->convertToMM('10mm');
        }
        $txt = ($param['label'] !== 'none' ? $this->parsingCss->value['font-size'] : false);
        $c = $this->parsingCss->value['color'];
        $infos = $this->pdf->myBarcode($param['value'], $param['type'], $x, $y, $w, $h, $txt, $c, $param['dimension']);

        $this->_maxX = max($this->_maxX, $x+$infos[0]);
        $this->_maxY = max($this->_maxY, $y+$infos[1]);
        $this->_maxH = max($this->_maxH, $infos[1]);
        $this->_maxE++;

        $this->pdf->SetXY($x+$infos[0], $y);

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : BARCODE
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_BARCODE($param)
    {
        // there is nothing to do here

        return true;
    }

    /**
     * tag : QRCODE
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_QRCODE($param)
    {
        if (!is_null($this->debug)) {
            $this->debug->addStep('QRCODE');
        }

        if (!isset($param['value'])) {
            $param['value'] = '';
        }
        if (!isset($param['ec'])) {
            $param['ec'] = 'H';
        }
        if (!isset($param['style']['color'])) {
            $param['style']['color'] = '#000000';
        }
        if (!isset($param['style']['background-color'])) {
            $param['style']['background-color'] = '#FFFFFF';
        }
        if (isset($param['style']['border'])) {
            $borders = $param['style']['border'] !== 'none';
            unset($param['style']['border']);
        } else {
            $borders = true;
        }

        if ($param['value'] === '') {
            return true;
        }
        if (!in_array($param['ec'], array('L', 'M', 'Q', 'H'))) {
            $param['ec'] = 'H';
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('qrcode', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $w = $this->parsingCss->value['width'];
        $h = $this->parsingCss->value['height'];
        $size = max($w, $h);
        if (!$size) {
            $size = $this->cssConverter->convertToMM('50mm');
        }

        $style = array(
                'fgcolor' => $this->parsingCss->value['color'],
                'bgcolor' => $this->parsingCss->value['background']['color'],
            );

        if ($borders) {
            $style['border'] = true;
            $style['padding'] = 'auto';
        } else {
            $style['border'] = false;
            $style['padding'] = 0;
        }

        if (!$this->_subPart && !$this->_isSubPart) {
            $this->pdf->write2DBarcode($param['value'], 'QRCODE,'.$param['ec'], $x, $y, $size, $size, $style);
        }

        $this->_maxX = max($this->_maxX, $x+$size);
        $this->_maxY = max($this->_maxY, $y+$size);
        $this->_maxH = max($this->_maxH, $size);
        $this->_maxE++;

        $this->pdf->SetX($x+$size);

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : QRCODE
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_QRCODE($param)
    {
        // there is nothing to do here

        return true;
    }

    /**
     * this is not a real TAG, it is just to write texts
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_WRITE($param)
    {
        $fill = ($this->parsingCss->value['background']['color'] !== null && $this->parsingCss->value['background']['image'] === null);
        if (in_array($this->parsingCss->value['id_tag'], array('fieldset', 'legend', 'div', 'table', 'tr', 'td', 'th'))) {
            $fill = false;
        }

        // get the text to write
        $txt = $param['txt'];

        if ($this->_isAfterFloat) {
            $txt = ltrim($txt);
            $this->_isAfterFloat = false;
        }

        $txt = str_replace('[[page_nb]]', $this->pdf->getMyAliasNbPages(), $txt);
        $txt = str_replace('[[page_cu]]', $this->pdf->getMyNumPage($this->_page), $txt);

        if ($this->parsingCss->value['text-transform'] !== 'none') {
            if ($this->parsingCss->value['text-transform'] === 'capitalize') {
                $txt = mb_convert_case($txt, MB_CASE_TITLE, $this->_encoding);
            } elseif ($this->parsingCss->value['text-transform'] === 'uppercase') {
                $txt = mb_convert_case($txt, MB_CASE_UPPER, $this->_encoding);
            } elseif ($this->parsingCss->value['text-transform'] === 'lowercase') {
                $txt = mb_convert_case($txt, MB_CASE_LOWER, $this->_encoding);
            }
        }

        // size of the text
        $h  = 1.08*$this->parsingCss->value['font-size'];
        $dh = $h*$this->parsingCss->value['mini-decal'];
        $lh = $this->parsingCss->getLineHeight();

        // identify the align
        $align = 'L';
        if ($this->parsingCss->value['text-align'] === 'li_right') {
            $w = $this->parsingCss->value['width'];
            $align = 'R';
        }

        // calculate the width of each words, and of all the sentence
        $w = 0;
        $words = explode(' ', $txt);
        foreach ($words as $k => $word) {
            $words[$k] = array($word, $this->pdf->GetStringWidth($word));
            $w+= $words[$k][1];
        }
        $space = $this->pdf->GetStringWidth(' ');
        $w+= $space*(count($words)-1);

        // position in the text
        $currPos = 0;

        // the bigger width of the text, after automatic break line
        $maxX = 0;

        // position of the text
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $dy = $this->_getElementY($lh);

        // margins
        list($left, $right) = $this->_getMargins($y);

        // number of lines after automatic break line
        $nb = 0;

        // while we have words, and the text does not fit on the line => we cut the sentence
        while ($x+$w>$right && $x<$right+$space && count($words)) {
            // adding words 1 by 1 to fit on the line
            $i=0;
            $old = array('', 0);
            $str = $words[0];
            $add = false;
            while (($x+$str[1])<$right) {
                $i++;
                $add = true;

                array_shift($words);
                $old = $str;

                if (!count($words)) {
                    break;
                }
                $str[0].= ' '.$words[0][0];
                $str[1]+= $space+$words[0][1];
            }
            $str = $old;

            // if nothing fits on the line, and if the first word does not fit on the line => the word is too long, we put it
            if ($i == 0 && (($left+$words[0][1])>=$right)) {
                $str = $words[0];
                array_shift($words);
                $i++;
                $add = true;
            }
            $currPos+= ($currPos ? 1 : 0)+strlen($str[0]);

            // write the extract sentence that fit on the page
            $wc = ($align === 'L' ? $str[1] : $this->parsingCss->value['width']);
            if ($right - $left<$wc) {
                $wc = $right - $left;
            }

            if (strlen($str[0])) {
                $this->pdf->SetXY($this->pdf->GetX(), $y+$dh+$dy);
                $this->pdf->Cell($wc, $h, $str[0], 0, 0, $align, $fill, $this->_isInLink);
                $this->pdf->SetXY($this->pdf->GetX(), $y);
            }
            $this->_maxH = max($this->_maxH, $lh);

            // max width
            $maxX = max($maxX, $this->pdf->GetX());

            // new position and new width for the "while"
            $w-= $str[1];
            $y = $this->pdf->GetY();
            $x = $this->pdf->GetX();
            $dy = $this->_getElementY($lh);

            // if we have again words to write
            if (count($words)) {
                // remove the space at the end
                if ($add) {
                    $w-= $space;
                }

                // if we don't add any word, and if the first word is empty => useless space to skip
                if (!$add && $words[0][0] === '') {
                    array_shift($words);
                }

                // if it is just to calculate for one line => adding the number of words
                if ($this->_isForOneLine) {
                    $this->_maxE+= $i;
                    $this->_maxX = max($this->_maxX, $maxX);
                    return null;
                }

                // automatic line break
                $this->_tag_open_BR(array('style' => ''), $currPos);

                // new position
                $y = $this->pdf->GetY();
                $x = $this->pdf->GetX();
                $dy = $this->_getElementY($lh);

                // if the next line does  not fit on the page => new page
                if ($y + $h>=$this->pdf->getH() - $this->pdf->getbMargin()) {
                    if (!$this->_isInOverflow && !$this->_isInFooter) {
                        $this->_setNewPage(null, '', null, $currPos);
                        $y = $this->pdf->GetY();
                        $x = $this->pdf->GetX();
                        $dy = $this->_getElementY($lh);
                    }
                }

                // if more than X line => error
                $nb++;
                if ($nb > $this->_sentenceMaxLines) {
                    $txt = '';
                    foreach ($words as $k => $word) {
                        $txt.= ($k ? ' ' : '').$word[0];
                    }
                    $e = new LongSentenceException(
                        'The current sentence takes more than '.$this->_sentenceMaxLines.' lines is the current box'
                    );
                    $e->setSentence($txt);
                    $e->setWidthBox($right-$left);
                    $e->setLength($w);
                    throw $e;
                }

                // new margins for the new line
                list($left, $right) = $this->_getMargins($y);
            }
        }

        // if we have words after automatic cut, it is because they fit on the line => we write the text
        if (count($words)) {
            $txt = '';
            foreach ($words as $k => $word) {
                $txt.= ($k ? ' ' : '').$word[0];
            }
            $w+= $this->pdf->getWordSpacing()*(count($words));
            $this->pdf->SetXY($this->pdf->GetX(), $y+$dh+$dy);
            $this->pdf->Cell(($align === 'L' ? $w : $this->parsingCss->value['width']), $h, $txt, 0, 0, $align, $fill, $this->_isInLink);
            $this->pdf->SetXY($this->pdf->GetX(), $y);
            $this->_maxH = max($this->_maxH, $lh);
            $this->_maxE+= count($words);
        }

        $maxX = max($maxX, $this->pdf->GetX());
        $maxY = $this->pdf->GetY()+$h;

        $this->_maxX = max($this->_maxX, $maxX);
        $this->_maxY = max($this->_maxY, $maxY);

        return true;
    }

    /**
     * tag : BR
     * mode : OPEN
     *
     * @param  array   $param
     * @param  integer $curr real position in the html parseur (if break line in the write of a text)
     * @return boolean
     */
    protected function _tag_open_BR($param, $curr = null)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $h = max($this->_maxH, $this->parsingCss->getLineHeight());

        if ($this->_maxH == 0) {
            $this->_maxY = max($this->_maxY, $this->pdf->GetY()+$h);
        }

        $this->_makeBreakLine($h, $curr);

        $this->_maxH = 0;
        $this->_maxE = 0;

        return true;
    }

    /**
     * tag : HR
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_HR($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }
        $oldAlign = $this->parsingCss->value['text-align'];
        $this->parsingCss->value['text-align'] = 'left';

        if ($this->_maxH) {
            $this->_tag_open_BR($param);
        }

        $fontSize = $this->parsingCss->value['font-size'];
        $this->parsingCss->value['font-size']=$fontSize*0.5;
        $this->_tag_open_BR($param);
        $this->parsingCss->value['font-size']=$fontSize;

        $param['style']['width'] = '100%';

        $this->parsingCss->save();
        $this->parsingCss->value['height']=$this->cssConverter->convertToMM('1mm');

        $this->parsingCss->analyse('hr', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $h = $this->parsingCss->value['height'];
        if ($h) {
            $h-= $this->parsingCss->value['border']['t']['width']+$this->parsingCss->value['border']['b']['width'];
        }
        if ($h<=0) {
            $h = $this->parsingCss->value['border']['t']['width']+$this->parsingCss->value['border']['b']['width'];
        }

        $this->_drawRectangle($this->pdf->GetX(), $this->pdf->GetY(), $this->parsingCss->value['width'], $h, $this->parsingCss->value['border'], 0, 0, $this->parsingCss->value['background']);
        $this->_maxH = $h;

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        $this->parsingCss->value['font-size'] = 0;
        $this->_tag_open_BR($param);

        $this->parsingCss->value['font-size']=$fontSize*0.5;
        $this->_tag_open_BR($param);
        $this->parsingCss->value['font-size']=$fontSize;

        $this->parsingCss->value['text-align'] = $oldAlign;
        $this->_setNewPositionForNewLine();

        return true;
    }

    /**
     * tag : A
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_A($param)
    {
        $this->_isInLink = str_replace('&amp;', '&', isset($param['href']) ? $param['href'] : '');

        if (isset($param['name'])) {
            $name =     $param['name'];
            if (!isset($this->_lstAnchor[$name])) {
                $this->_lstAnchor[$name] = array($this->pdf->AddLink(), false);
            }

            if (!$this->_lstAnchor[$name][1]) {
                $this->_lstAnchor[$name][1] = true;
                $this->pdf->SetLink($this->_lstAnchor[$name][0], -1, -1);
            }
        }

        if (preg_match('/^#([^#]+)$/isU', $this->_isInLink, $match)) {
            $name = $match[1];
            if (!isset($this->_lstAnchor[$name])) {
                $this->_lstAnchor[$name] = array($this->pdf->AddLink(), false);
            }

            $this->_isInLink = $this->_lstAnchor[$name][0];
        }

        $this->parsingCss->save();
        $this->parsingCss->value['font-underline'] = true;
        $this->parsingCss->value['color'] = array(20, 20, 250);
        $this->parsingCss->analyse('a', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : A
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_A($param)
    {
        $this->_isInLink    = '';
        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : H1
     * mode : OPEN
     *
     * @param  array $param
     * @param  string $other
     * @return boolean
     */
    protected function _tag_open_H1($param, $other = 'h1')
    {
        if ($this->_isForOneLine) {
            return false;
        }

        if ($this->_maxH) {
            $this->_tag_open_BR(array());
        }
        $this->parsingCss->save();
        $this->parsingCss->value['font-bold'] = true;

        $size = array('h1' => '28px', 'h2' => '24px', 'h3' => '20px', 'h4' => '16px', 'h5' => '12px', 'h6' => '9px');
        $this->parsingCss->value['margin']['l'] = 0;
        $this->parsingCss->value['margin']['r'] = 0;
        $this->parsingCss->value['margin']['t'] = $this->cssConverter->convertToMM('16px');
        $this->parsingCss->value['margin']['b'] = $this->cssConverter->convertToMM('16px');
        $this->parsingCss->value['font-size'] = $this->cssConverter->convertFontSize($size[$other]);

        $this->parsingCss->analyse($other, $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();
        $this->_setNewPositionForNewLine();

        return true;
    }

    /**
     * tag : H2
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_H2($param)
    {
        return $this->_tag_open_H1($param, 'h2');
    }

    /**
     * tag : H3
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_H3($param)
    {
        return $this->_tag_open_H1($param, 'h3');
    }

    /**
     * tag : H4
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_H4($param)
    {
        return $this->_tag_open_H1($param, 'h4');
    }

    /**
     * tag : H5
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_H5($param)
    {
        return $this->_tag_open_H1($param, 'h5');
    }

    /**
     * tag : H6
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_H6($param)
    {
        return $this->_tag_open_H1($param, 'h6');
    }

    /**
     * tag : H1
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_H1($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH+= $this->parsingCss->value['margin']['b'];
        $h = max($this->_maxH, $this->parsingCss->getLineHeight());

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        $this->_makeBreakLine($h);
        $this->_maxH = 0;

        $this->_maxY = max($this->_maxY, $this->pdf->GetY());

        return true;
    }

    /**
     * tag : H2
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_H2($param)
    {
        return $this->_tag_close_H1($param);
    }

    /**
     * tag : H3
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_H3($param)
    {
        return $this->_tag_close_H1($param);
    }

    /**
     * tag : H4
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_H4($param)
    {
        return $this->_tag_close_H1($param);
    }

    /**
     * tag : H5
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_H5($param)
    {
        return $this->_tag_close_H1($param);
    }

    /**
     * tag : H6
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_H6($param)
    {
        return $this->_tag_close_H1($param);
    }

    /**
     * tag : P
     * mode : OPEN
     *
     * @param    array $param
     * @return boolean
     */
    protected function _tag_open_P($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        if (!in_array($this->_previousCall, array('_tag_close_P', '_tag_close_UL'))) {
            if ($this->_maxH) {
                $this->_tag_open_BR(array());
            }
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('p', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

         // cancel the effects of the setPosition
        $this->pdf->SetXY($this->pdf->GetX()-$this->parsingCss->value['margin']['l'], $this->pdf->GetY()-$this->parsingCss->value['margin']['t']);

        list($mL, $mR) = $this->_getMargins($this->pdf->GetY());
        $mR = $this->pdf->getW()-$mR;
        $mL+= $this->parsingCss->value['margin']['l']+$this->parsingCss->value['padding']['l'];
        $mR+= $this->parsingCss->value['margin']['r']+$this->parsingCss->value['padding']['r'];
        $this->_saveMargin($mL, 0, $mR);

        if ($this->parsingCss->value['text-indent']>0) {
            $y = $this->pdf->GetY()+$this->parsingCss->value['margin']['t']+$this->parsingCss->value['padding']['t'];
            $this->_pageMarges[floor($y*100)] = array($mL+$this->parsingCss->value['text-indent'], $this->pdf->getW()-$mR);
            $y+= $this->parsingCss->getLineHeight()*0.1;
            $this->_pageMarges[floor($y*100)] = array($mL, $this->pdf->getW()-$mR);
        }
        $this->_makeBreakLine($this->parsingCss->value['margin']['t']+$this->parsingCss->value['padding']['t']);
        $this->_isInParagraph = array($mL, $mR);
        return true;
    }

    /**
     * tag : P
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_P($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        if ($this->_maxH) {
            $this->_tag_open_BR(array());
        }
        $this->_isInParagraph = false;
        $this->_loadMargin();
        $h = $this->parsingCss->value['margin']['b']+$this->parsingCss->value['padding']['b'];

        $this->parsingCss->load();
        $this->parsingCss->fontSet();
        $this->_makeBreakLine($h);

        return true;
    }

    /**
     * tag : PRE
     * mode : OPEN
     *
     * @param  array $param
     * @param  string $other
     * @return boolean
     */
    protected function _tag_open_PRE($param, $other = 'pre')
    {
        if ($other === 'pre' && $this->_maxH) {
            $this->_tag_open_BR(array());
        }

        $this->parsingCss->save();
        $this->parsingCss->value['font-family'] = 'courier';
        $this->parsingCss->analyse($other, $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        if ($other === 'pre') {
            return $this->_tag_open_DIV($param, $other);
        }

        return true;
    }

    /**
     * tag : CODE
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_CODE($param)
    {
        return $this->_tag_open_PRE($param, 'code');
    }

    /**
     * tag : PRE
     * mode : CLOSE
     *
     * @param  array $param
     * @param  string $other
     * @return boolean
     */
    protected function _tag_close_PRE($param, $other = 'pre')
    {
        if ($other === 'pre') {
            if ($this->_isForOneLine) {
                return false;
            }

            $this->_tag_close_DIV($param, $other);
            $this->_tag_open_BR(array());
        }
        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : CODE
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_CODE($param)
    {
        return $this->_tag_close_PRE($param, 'code');
    }

    /**
     * tag : UL
     * mode : OPEN
     *
     * @param  array $param
     * @param  string $other
     * @return boolean
     */
    protected function _tag_open_UL($param, $other = 'ul')
    {
        if ($this->_isForOneLine) {
            return false;
        }

        if (!in_array($this->_previousCall, array('_tag_close_P', '_tag_close_UL'))) {
            if ($this->_maxH) {
                $this->_tag_open_BR(array());
            }
            if (!count($this->_defList)) {
                $this->_tag_open_BR(array());
            }
        }

        if (!isset($param['style']['width'])) {
            $param['allwidth'] = true;
        }
        $param['cellspacing'] = 0;

        // a list is like a table
        $this->_tag_open_TABLE($param, $other);

        // add a level of list
        $start = (isset($this->parsingCss->value['start']) ? $this->parsingCss->value['start'] : null);
        $this->_listeAddLevel($other, $this->parsingCss->value['list-style-type'], $this->parsingCss->value['list-style-image'], $start);

        return true;
    }

    /**
     * tag : OL
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_OL($param)
    {
        return $this->_tag_open_UL($param, 'ol');
    }

    /**
     * tag : UL
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_UL($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_tag_close_TABLE($param);

        $this->_listeDelLevel();

        if (!$this->_subPart) {
            if (!count($this->_defList)) {
                $this->_tag_open_BR(array());
            }
        }

        return true;
    }

    /**
     * tag : OL
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_OL($param)
    {
        return $this->_tag_close_UL($param);
    }

    /**
     * tag : LI
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_LI($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_listeAddLi();

        if (!isset($param['style']['width'])) {
            $param['style']['width'] = '100%';
        }

        $paramPUCE = $param;

        $inf = $this->_listeGetLi();
        if ($inf[0]) {
            if ($inf[0] === 'zapfdingbats') {
                // ensure the correct icon is used despite external css rules
                $paramPUCE['style']['text-transform']  = 'lowercase';
            }
            $paramPUCE['style']['font-family']     = $inf[0];
            $paramPUCE['style']['text-align']      = 'li_right';
            $paramPUCE['style']['vertical-align']  = 'top';
            $paramPUCE['style']['width']           = $this->_listeGetWidth();
            $paramPUCE['style']['padding-right']   = $this->_listeGetPadding();
            $paramPUCE['txt'] = $inf[2];
        } else {
            $paramPUCE['style']['text-align']      = 'li_right';
            $paramPUCE['style']['vertical-align']  = 'top';
            $paramPUCE['style']['width']           = $this->_listeGetWidth();
            $paramPUCE['style']['padding-right']   = $this->_listeGetPadding();
            $paramPUCE['src'] = $inf[2];
            $paramPUCE['sub_li'] = true;
        }

        $this->_tag_open_TR($param, 'li');

        $this->parsingCss->save();

        // if small LI
        if ($inf[1]) {
            $this->parsingCss->value['mini-decal']+= $this->parsingCss->value['mini-size']*0.045;
            $this->parsingCss->value['mini-size'] *= 0.75;
        }

        // if we are in a sub html => prepare. Else : display
        if ($this->_subPart) {
            // TD for the puce
            $tmpPos = $this->_tempPos;
            $tmpLst1 = $this->parsingHtml->code[$tmpPos+1];
            $tmpLst2 = $this->parsingHtml->code[$tmpPos+2];

            $name = isset($paramPUCE['src']) ? 'img' : 'write';
            $params = $paramPUCE;
            unset($params['style']['width']);
            $this->parsingHtml->code[$tmpPos+1] = new Node($name, $params, false);
            $this->parsingHtml->code[$tmpPos+2] = new Node('li', $paramPUCE, true);
            $this->_tag_open_TD($paramPUCE, 'li_sub');
            $this->_tag_close_TD($param);
            $this->_tempPos = $tmpPos;
            $this->parsingHtml->code[$tmpPos+1] = $tmpLst1;
            $this->parsingHtml->code[$tmpPos+2] = $tmpLst2;
        } else {
            // TD for the puce
            $this->_tag_open_TD($paramPUCE, 'li_sub');
            unset($paramPUCE['style']['width']);
            if (isset($paramPUCE['src'])) {
                $this->_tag_open_IMG($paramPUCE);
            } else {
                $this->_tag_open_WRITE($paramPUCE);
            }
            $this->_tag_close_TD($paramPUCE);
        }
        $this->parsingCss->load();


        // TD for the content
        $this->_tag_open_TD($param, 'li');

        return true;
    }

    /**
     * tag : LI
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_LI($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_tag_close_TD($param);

        $this->_tag_close_TR($param);

        return true;
    }

    /**
     * tag : TBODY
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_TBODY($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('tbody', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : TBODY
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TBODY($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : THEAD
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_THEAD($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('thead', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        // if we are in a sub part, save the number of the first TR in the thead
        if ($this->_subPart) {
            self::$_tables[$param['num']]['thead']['tr'][0] = self::$_tables[$param['num']]['tr_curr'];
            self::$_tables[$param['num']]['thead']['code'] = array();

            $amountHtmlCodes = count($this->parsingHtml->code);
            for ($pos=$this->_tempPos; $pos<$amountHtmlCodes; $pos++) {
                $action = clone $this->parsingHtml->code[$pos];
                if (strtolower($action->getName()) === 'thead') {
                    $action->setName('thead_sub');
                }
                self::$_tables[$param['num']]['thead']['code'][] = $action;
                if (strtolower($action->getName()) === 'thead_sub' && $action->isClose()) {
                    break;
                }
            }
        } else {
            $level = $this->parsingHtml->getLevel($this->_parsePos);
            $this->_parsePos+= count($level);
            self::$_tables[$param['num']]['tr_curr']+= count(self::$_tables[$param['num']]['thead']['tr']);
        }

        return true;
    }

    /**
     * tag : THEAD
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_THEAD($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        // if we are in a sub HTM, construct the list of the TR in the thead
        if ($this->_subPart) {
            $min = self::$_tables[$param['num']]['thead']['tr'][0];
            $max = self::$_tables[$param['num']]['tr_curr']-1;
            self::$_tables[$param['num']]['thead']['tr'] = range($min, $max);
        }

        return true;
    }

    /**
     * tag : TFOOT
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_TFOOT($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('tfoot', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        // if we are in a sub part, save the number of the first TR in the tfoot
        if ($this->_subPart) {
            self::$_tables[$param['num']]['tfoot']['tr'][0] = self::$_tables[$param['num']]['tr_curr'];
            self::$_tables[$param['num']]['tfoot']['code'] = array();

            $amountHtmlCodes = count($this->parsingHtml->code);
            for ($pos=$this->_tempPos; $pos<$amountHtmlCodes; $pos++) {
                $action = clone $this->parsingHtml->code[$pos];
                if (strtolower($action->getName()) === 'tfoot') {
                    $action->setName('tfoot_sub');
                }
                self::$_tables[$param['num']]['tfoot']['code'][] = $action;
                if (strtolower($action->getName()) === 'tfoot_sub' && $action->isClose()) {
                    break;
                }
            }
        } else {
            $level = $this->parsingHtml->getLevel($this->_parsePos);
            $this->_parsePos+= count($level);
            self::$_tables[$param['num']]['tr_curr']+= count(self::$_tables[$param['num']]['tfoot']['tr']);
        }

        return true;
    }

    /**
     * tag : TFOOT
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TFOOT($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        // if we are in a sub HTM, construct the list of the TR in the tfoot
        if ($this->_subPart) {
            $min = self::$_tables[$param['num']]['tfoot']['tr'][0];
            $max = self::$_tables[$param['num']]['tr_curr']-1;
            self::$_tables[$param['num']]['tfoot']['tr'] = range($min, $max);
        }

        return true;
    }

    /**
     * It is not a real TAG, do not use it !
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_THEAD_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('thead', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * It is not a real TAG, do not use it !
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_THEAD_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * It is not a real TAG, do not use it !
     *
     * @param    array $param
     * @return boolean
     */
    protected function _tag_open_TFOOT_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('tfoot', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * It is not a real TAG, do not use it !
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TFOOT_SUB($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : FORM
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_FORM($param)
    {
        $this->parsingCss->save();
        $this->parsingCss->analyse('form', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $this->pdf->setFormDefaultProp(
            array(
                'lineWidth'=>1,
                'borderStyle'=>'solid',
                'fillColor'=>array(220, 220, 255),
                'strokeColor'=>array(128, 128, 200)
            )
        );

        $this->_isInForm = isset($param['action']) ? $param['action'] : '';

        return true;
    }

    /**
     * tag : FORM
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_FORM($param)
    {
        $this->_isInForm = false;
        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : TABLE
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_TABLE($param, $other = 'table')
    {
        if ($this->_maxH) {
            if ($this->_isForOneLine) {
                return false;
            }
            $this->_tag_open_BR(array());
        }

        if ($this->_isForOneLine) {
            $this->_maxX = $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();
            return false;
        }

        $this->_maxH = 0;

        $alignObject = isset($param['align']) ? strtolower($param['align']) : 'left';
        if (isset($param['align'])) {
            unset($param['align']);
        }
        if (!in_array($alignObject, array('left', 'center', 'right'))) {
            $alignObject = 'left';
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse($other, $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        if ($this->parsingCss->value['margin-auto']) {
            $alignObject = 'center';
        }

        // collapse table ?
        $collapse = false;
        if ($other === 'table') {
            $collapse = isset($this->parsingCss->value['border']['collapse']) ? $this->parsingCss->value['border']['collapse'] : false;
        }

        // if collapse => no borders for the table, only for TD
        if ($collapse) {
            $param['style']['border'] = 'none';
            $param['cellspacing'] = 0;
            $none = $this->parsingCss->readBorder('none');
            $this->parsingCss->value['border']['t'] = $none;
            $this->parsingCss->value['border']['r'] = $none;
            $this->parsingCss->value['border']['b'] = $none;
            $this->parsingCss->value['border']['l'] = $none;
        }

        // if we are in a SUB html => prepare the properties of the table
        if ($this->_subPart) {
            if (!is_null($this->debug)) {
                $this->debug->addStep('Table '.$param['num'], true);
            }
            self::$_tables[$param['num']] = array();
            self::$_tables[$param['num']]['border']          = isset($param['border']) ? $this->parsingCss->readBorder($param['border']) : null;
            self::$_tables[$param['num']]['cellpadding']     = $this->cssConverter->convertToMM(isset($param['cellpadding']) ? $param['cellpadding'] : '1px');
            self::$_tables[$param['num']]['cellspacing']     = $this->cssConverter->convertToMM(isset($param['cellspacing']) ? $param['cellspacing'] : '2px');
            self::$_tables[$param['num']]['cases']           = array();          // properties of each TR/TD
            self::$_tables[$param['num']]['corr']            = array();          // link between TR/TD and colspan/rowspan
            self::$_tables[$param['num']]['corr_x']          = 0;                // position in 'cases'
            self::$_tables[$param['num']]['corr_y']          = 0;                // position in 'cases'
            self::$_tables[$param['num']]['td_curr']         = 0;                // current column
            self::$_tables[$param['num']]['tr_curr']         = 0;                // current row
            self::$_tables[$param['num']]['curr_x']          = $this->pdf->GetX();
            self::$_tables[$param['num']]['curr_y']          = $this->pdf->GetY();
            self::$_tables[$param['num']]['width']           = 0;                // global width
            self::$_tables[$param['num']]['height']          = 0;                // global height
            self::$_tables[$param['num']]['align']           = $alignObject;
            self::$_tables[$param['num']]['marge']           = array();
            self::$_tables[$param['num']]['marge']['t']      = $this->parsingCss->value['padding']['t']+$this->parsingCss->value['border']['t']['width']+self::$_tables[$param['num']]['cellspacing']*0.5;
            self::$_tables[$param['num']]['marge']['r']      = $this->parsingCss->value['padding']['r']+$this->parsingCss->value['border']['r']['width']+self::$_tables[$param['num']]['cellspacing']*0.5;
            self::$_tables[$param['num']]['marge']['b']      = $this->parsingCss->value['padding']['b']+$this->parsingCss->value['border']['b']['width']+self::$_tables[$param['num']]['cellspacing']*0.5;
            self::$_tables[$param['num']]['marge']['l']      = $this->parsingCss->value['padding']['l']+$this->parsingCss->value['border']['l']['width']+self::$_tables[$param['num']]['cellspacing']*0.5;
            self::$_tables[$param['num']]['page']            = 0;                // number of pages
            self::$_tables[$param['num']]['new_page']        = true;             // flag : new page for the current TR
            self::$_tables[$param['num']]['style_value']     = null;             // CSS style of the table
            self::$_tables[$param['num']]['thead']           = array();          // properties on the thead
            self::$_tables[$param['num']]['tfoot']           = array();          // properties on the tfoot
            self::$_tables[$param['num']]['thead']['tr']     = array();          // list of the TRs in the thead
            self::$_tables[$param['num']]['tfoot']['tr']     = array();          // list of the TRs in the tfoot
            self::$_tables[$param['num']]['thead']['height']    = 0;             // thead height
            self::$_tables[$param['num']]['tfoot']['height']    = 0;             // tfoot height
            self::$_tables[$param['num']]['thead']['code'] = array();            // HTML content of the thead
            self::$_tables[$param['num']]['tfoot']['code'] = array();            // HTML content of the tfoot
            self::$_tables[$param['num']]['cols']        = array();              // properties of the COLs

            $this->_saveMargin($this->pdf->getlMargin(), $this->pdf->gettMargin(), $this->pdf->getrMargin());

            $this->parsingCss->value['width']-= self::$_tables[$param['num']]['marge']['l'] + self::$_tables[$param['num']]['marge']['r'];
        } else {
            // we start from the first page and the first page of the table
            self::$_tables[$param['num']]['page'] = 0;
            self::$_tables[$param['num']]['td_curr']    = 0;
            self::$_tables[$param['num']]['tr_curr']    = 0;
            self::$_tables[$param['num']]['td_x']        = self::$_tables[$param['num']]['marge']['l']+self::$_tables[$param['num']]['curr_x'];
            self::$_tables[$param['num']]['td_y']        = self::$_tables[$param['num']]['marge']['t']+self::$_tables[$param['num']]['curr_y'];

            // draw the borders/background of the first page/part of the table
            $this->_drawRectangle(
                self::$_tables[$param['num']]['curr_x'],
                self::$_tables[$param['num']]['curr_y'],
                self::$_tables[$param['num']]['width'],
                isset(self::$_tables[$param['num']]['height'][0]) ? self::$_tables[$param['num']]['height'][0] : null,
                $this->parsingCss->value['border'],
                $this->parsingCss->value['padding'],
                0,
                $this->parsingCss->value['background']
            );

            self::$_tables[$param['num']]['style_value'] = $this->parsingCss->value;
        }

        return true;
    }

    /**
     * tag : TABLE
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TABLE($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        // if we are in a sub HTML
        if ($this->_subPart) {
            // calculate the size of each case
            $this->_calculateTableCellSize(self::$_tables[$param['num']]['cases'], self::$_tables[$param['num']]['corr']);

            // calculate the height of the thead and the tfoot
            $lst = array('thead', 'tfoot');
            foreach ($lst as $mode) {
                self::$_tables[$param['num']][$mode]['height'] = 0;
                foreach (self::$_tables[$param['num']][$mode]['tr'] as $tr) {
                    // hauteur de la ligne tr
                    $h = 0;
                    $nbTrs = count(self::$_tables[$param['num']]['cases'][$tr]);
                    for ($i=0; $i<$nbTrs; $i++) {
                        if (self::$_tables[$param['num']]['cases'][$tr][$i]['rowspan'] == 1) {
                            $h = max($h, self::$_tables[$param['num']]['cases'][$tr][$i]['h']);
                        }
                    }
                    self::$_tables[$param['num']][$mode]['height']+= $h;
                }
            }

            // calculate the width of the table
            self::$_tables[$param['num']]['width'] = self::$_tables[$param['num']]['marge']['l'] + self::$_tables[$param['num']]['marge']['r'];
            if (isset(self::$_tables[$param['num']]['cases'][0])) {
                foreach (self::$_tables[$param['num']]['cases'][0] as $case) {
                    self::$_tables[$param['num']]['width']+= $case['w'];
                }
            }

            // X position of the table
            $old = $this->parsingCss->getOldValues();
            $parentWidth = $old['width'] ? $old['width'] : $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();
            $x = self::$_tables[$param['num']]['curr_x'];
            $w = self::$_tables[$param['num']]['width'];
            if ($parentWidth>$w) {
                if (self::$_tables[$param['num']]['align'] === 'center') {
                    $x = $x + ($parentWidth-$w)*0.5;
                } elseif (self::$_tables[$param['num']]['align'] === 'right') {
                    $x = $x + $parentWidth-$w;
                }

                self::$_tables[$param['num']]['curr_x'] = $x;
            }

            // calculate the height of the table
            self::$_tables[$param['num']]['height'] = array();

            // minimum of the height because of margins, and of the thead and tfoot height
            $h0 = self::$_tables[$param['num']]['marge']['t'] + self::$_tables[$param['num']]['marge']['b'];
            $h0+= self::$_tables[$param['num']]['thead']['height'] + self::$_tables[$param['num']]['tfoot']['height'];

            // max height of the page
            $max = $this->pdf->getH() - $this->pdf->getbMargin();

            // current position on the page
            $y = self::$_tables[$param['num']]['curr_y'];
            $height = $h0;

            // we get the height of each line
            $nbCases = count(self::$_tables[$param['num']]['cases']);
            for ($k=0; $k<$nbCases; $k++) {

                // if it is a TR of the thead or of the tfoot => skip
                if (in_array($k, self::$_tables[$param['num']]['thead']['tr'])) {
                    continue;
                }
                if (in_array($k, self::$_tables[$param['num']]['tfoot']['tr'])) {
                    continue;
                }

                // height of the line
                $th = 0;
                $h = 0;
                $nbCasesK = count(self::$_tables[$param['num']]['cases'][$k]);
                for ($i=0; $i<$nbCasesK; $i++) {
                    $h = max($h, self::$_tables[$param['num']]['cases'][$k][$i]['h']);

                    if (self::$_tables[$param['num']]['cases'][$k][$i]['rowspan'] == 1) {
                        $th = max($th, self::$_tables[$param['num']]['cases'][$k][$i]['h']);
                    }
                }

                // if the row does not fit on the page => new page
                if ($y+$h+$height>$max) {
                    if ($height == $h0) {
                        $height = null;
                    }
                    self::$_tables[$param['num']]['height'][] = $height;
                    $height = $h0;
                    $y = $this->_margeTop;
                }
                $height+= $th;
            }

            // if ther is a height at the end, add it
            if ($height !=$h0 || $k == 0) {
                self::$_tables[$param['num']]['height'][] = $height;
            }
        } else {
            // if we have tfoot, draw it
            if (count(self::$_tables[$param['num']]['tfoot']['code'])) {
                $tmpTR = self::$_tables[$param['num']]['tr_curr'];
                $tmpTD = self::$_tables[$param['num']]['td_curr'];
                $oldParsePos = $this->_parsePos;
                $oldParseCode = $this->parsingHtml->code;

                self::$_tables[$param['num']]['tr_curr'] = self::$_tables[$param['num']]['tfoot']['tr'][0];
                self::$_tables[$param['num']]['td_curr'] = 0;
                $this->_parsePos = 0;
                $this->parsingHtml->code = self::$_tables[$param['num']]['tfoot']['code'];
                $this->_isInTfoot = true;
                $this->_makeHTMLcode();
                $this->_isInTfoot = false;

                $this->_parsePos =     $oldParsePos;
                $this->parsingHtml->code = $oldParseCode;
                self::$_tables[$param['num']]['tr_curr'] = $tmpTR;
                self::$_tables[$param['num']]['td_curr'] = $tmpTD;
            }

            // get the positions of the end of the table
            $x = self::$_tables[$param['num']]['curr_x'] + self::$_tables[$param['num']]['width'];
            if (count(self::$_tables[$param['num']]['height'])>1) {
                $y = $this->_margeTop+self::$_tables[$param['num']]['height'][count(self::$_tables[$param['num']]['height'])-1];
            } elseif (count(self::$_tables[$param['num']]['height']) == 1) {
                $y = self::$_tables[$param['num']]['curr_y']+self::$_tables[$param['num']]['height'][count(self::$_tables[$param['num']]['height'])-1];
            } else {
                $y = self::$_tables[$param['num']]['curr_y'];
            }

            $y+= $this->parsingCss->value['margin']['b'];

            $this->_maxX = max($this->_maxX, $x);
            $this->_maxY = max($this->_maxY, $y);

            $this->pdf->SetXY($this->pdf->getlMargin(), $y);

            $this->_loadMargin();

            if (!is_null($this->debug)) {
                $this->debug->addStep('Table '.$param['num'], false);
            }
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();


        return true;
    }

    /**
     * tag : COL
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_COL($param)
    {
        $span = isset($param['span']) ? $param['span'] : 1;
        for ($k=0; $k<$span; $k++) {
            self::$_tables[$param['num']]['cols'][] = $param;
        }

        return true;
    }

    /**
     * tag : COL
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_COL($param)
    {
        // there is nothing to do here

        return true;
    }

    /**
     * tag : COLGROUP
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_COLGROUP($param)
    {
        // there is nothing to do here

        return true;
    }

    /**
     * tag : COLGROUP
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_COLGROUP($param)
    {
        // there is nothing to do here

        return true;
    }

    /**
     * tag : TR
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_TR($param, $other = 'tr')
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        $this->parsingCss->save();
        $this->parsingCss->analyse($other, $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        // position in the table
        self::$_tables[$param['num']]['tr_curr']++;
        self::$_tables[$param['num']]['td_curr']= 0;

        // if we are not in a sub html
        if (!$this->_subPart) {

            // Y after the row
            $ty=null;
            $nbTrCurrs = count(self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1]);
            for ($ii=0; $ii<$nbTrCurrs; $ii++) {
                $ty = max($ty, self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][$ii]['h']);
            }

            // height of the tfoot
            $hfoot = self::$_tables[$param['num']]['tfoot']['height'];

            // if the line does not fit on the page => new page
            if (!$this->_isInTfoot && self::$_tables[$param['num']]['td_y'] + self::$_tables[$param['num']]['marge']['b'] + $ty +$hfoot> $this->pdf->getH() - $this->pdf->getbMargin()) {

                // fi ther is a tfoot => draw it
                if (count(self::$_tables[$param['num']]['tfoot']['code'])) {
                    $tmpTR = self::$_tables[$param['num']]['tr_curr'];
                    $tmpTD = self::$_tables[$param['num']]['td_curr'];
                    $oldParsePos = $this->_parsePos;
                    $oldParseCode = $this->parsingHtml->code;

                    self::$_tables[$param['num']]['tr_curr'] = self::$_tables[$param['num']]['tfoot']['tr'][0];
                    self::$_tables[$param['num']]['td_curr'] = 0;
                    $this->_parsePos = 0;
                    $this->parsingHtml->code = self::$_tables[$param['num']]['tfoot']['code'];
                    $this->_isInTfoot = true;
                    $this->_makeHTMLcode();
                    $this->_isInTfoot = false;

                    $this->_parsePos =     $oldParsePos;
                    $this->parsingHtml->code = $oldParseCode;
                    self::$_tables[$param['num']]['tr_curr'] = $tmpTR;
                    self::$_tables[$param['num']]['td_curr'] = $tmpTD;
                }

                // new page
                self::$_tables[$param['num']]['new_page'] = true;
                $this->_setNewPage();

                // new position
                self::$_tables[$param['num']]['page']++;
                self::$_tables[$param['num']]['curr_y'] = $this->pdf->GetY();
                self::$_tables[$param['num']]['td_y'] = self::$_tables[$param['num']]['curr_y']+self::$_tables[$param['num']]['marge']['t'];

                // if we have the height of the tbale on the page => draw borders and background
                if (isset(self::$_tables[$param['num']]['height'][self::$_tables[$param['num']]['page']])) {
                    $old = $this->parsingCss->value;
                    $this->parsingCss->value = self::$_tables[$param['num']]['style_value'];

                    $this->_drawRectangle(
                        self::$_tables[$param['num']]['curr_x'],
                        self::$_tables[$param['num']]['curr_y'],
                        self::$_tables[$param['num']]['width'],
                        self::$_tables[$param['num']]['height'][self::$_tables[$param['num']]['page']],
                        $this->parsingCss->value['border'],
                        $this->parsingCss->value['padding'],
                        self::$_tables[$param['num']]['cellspacing']*0.5,
                        $this->parsingCss->value['background']
                    );

                    $this->parsingCss->value = $old;
                }
            }

            // if we are in a new page, and if we have a thead => draw it
            if (self::$_tables[$param['num']]['new_page'] && count(self::$_tables[$param['num']]['thead']['code'])) {
                self::$_tables[$param['num']]['new_page'] = false;
                $tmpTR = self::$_tables[$param['num']]['tr_curr'];
                $tmpTD = self::$_tables[$param['num']]['td_curr'];
                $oldParsePos = $this->_parsePos;
                $oldParseCode = $this->parsingHtml->code;

                self::$_tables[$param['num']]['tr_curr'] = self::$_tables[$param['num']]['thead']['tr'][0];
                self::$_tables[$param['num']]['td_curr'] = 0;
                $this->_parsePos = 0;
                $this->parsingHtml->code = self::$_tables[$param['num']]['thead']['code'];
                $this->_isInThead = true;
                $this->_makeHTMLcode();
                $this->_isInThead = false;

                $this->_parsePos =     $oldParsePos;
                $this->parsingHtml->code = $oldParseCode;
                self::$_tables[$param['num']]['tr_curr'] = $tmpTR;
                self::$_tables[$param['num']]['td_curr'] = $tmpTD;
                self::$_tables[$param['num']]['new_page'] = true;
            }
        // else (in a sub HTML)
        } else {
            // prepare it
            self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1] = array();
            if (!isset(self::$_tables[$param['num']]['corr'][self::$_tables[$param['num']]['corr_y']])) {
                self::$_tables[$param['num']]['corr'][self::$_tables[$param['num']]['corr_y']] = array();
            }

            self::$_tables[$param['num']]['corr_x']=0;
            while (isset(self::$_tables[$param['num']]['corr'][self::$_tables[$param['num']]['corr_y']][self::$_tables[$param['num']]['corr_x']])) {
                self::$_tables[$param['num']]['corr_x']++;
            }
        }

        return true;
    }

    /**
     * tag : TR
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TR($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        // if we are not in a sub HTML
        if (!$this->_subPart) {

            // Y of the current line
            $ty=null;
            $nbTrCurrs = count(self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1]);
            for ($ii=0; $ii<$nbTrCurrs; $ii++) {
                if (self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][$ii]['rowspan'] == 1) {
                    $ty = self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][$ii]['h'];
                }
            }

            // new position
            self::$_tables[$param['num']]['td_x'] = self::$_tables[$param['num']]['curr_x']+self::$_tables[$param['num']]['marge']['l'];
            self::$_tables[$param['num']]['td_y']+= $ty;
            self::$_tables[$param['num']]['new_page'] = false;
        } else {
            self::$_tables[$param['num']]['corr_y']++;
        }

        return true;
    }

    /**
     * tag : TD
     * mode : OPEN
     *
     * @param  array $param
     * @param string $other
     *
     * @return boolean
     */
    protected function _tag_open_TD($param, $other = 'td')
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        $param['cellpadding'] = self::$_tables[$param['num']]['cellpadding'].'mm';
        $param['cellspacing'] = self::$_tables[$param['num']]['cellspacing'].'mm';

        // specific style for LI
        if ($other === 'li') {
            $specialLi = true;
        } else {
            $specialLi = false;
            if ($other === 'li_sub') {
                $param['style']['border'] = 'none';
                $param['style']['background-color']    = 'transparent';
                $param['style']['background-image']    = 'none';
                $param['style']['background-position'] = '';
                $param['style']['background-repeat']   = '';
                $other = 'li';
            }
        }

        // get the properties of the TD
        $x = self::$_tables[$param['num']]['td_curr'];
        $y = self::$_tables[$param['num']]['tr_curr']-1;
        $colspan = isset($param['colspan']) ? $param['colspan'] : 1;
        $rowspan = isset($param['rowspan']) ? $param['rowspan'] : 1;

        // flag for collapse table
        $collapse = false;

        // specific treatment for TD and TH
        if (in_array($other, array('td', 'th'))) {
            // id of the column
            $numCol = isset(self::$_tables[$param['num']]['cases'][$y][$x]['Xr']) ? self::$_tables[$param['num']]['cases'][$y][$x]['Xr'] : self::$_tables[$param['num']]['corr_x'];

            // we get the properties of the COL tag, if exist
            if (isset(self::$_tables[$param['num']]['cols'][$numCol])) {

                $colParam = self::$_tables[$param['num']]['cols'][$numCol];

                // for colspans => we get all the needed widths
                $colParam['style']['width'] = array();
                for ($k=0; $k<$colspan; $k++) {
                    if (isset(self::$_tables[$param['num']]['cols'][$numCol+$k]['style']['width'])) {
                        $colParam['style']['width'][] = self::$_tables[$param['num']]['cols'][$numCol+$k]['style']['width'];
                    }
                }

                // calculate the total width of the column
                $total = '';
                $last = $this->parsingCss->getLastWidth();
                if (count($colParam['style']['width'])) {
                    $total = $colParam['style']['width'][0];
                    unset($colParam['style']['width'][0]);
                    foreach ($colParam['style']['width'] as $width) {
                        if (substr($total, -1) === '%' && substr($width, -1) === '%') {
                            $total = (str_replace('%', '', $total)+str_replace('%', '', $width)).'%';
                        } else {
                            $total = ($this->cssConverter->convertToMM($total, $last) + $this->cssConverter->convertToMM($width, $last)).'mm';
                        }
                    }
                }

                // get the final width
                if ($total) {
                    $colParam['style']['width'] = $total;
                } else {
                    unset($colParam['style']['width']);
                }


                // merge the styles of the COL and the TD
                $param['style'] = array_merge($colParam['style'], $param['style']);

                // merge the class of the COL and the TD
                if (isset($colParam['class'])) {
                    $param['class'] = $colParam['class'].(isset($param['class']) ? ' '.$param['class'] : '');
                }
            }

            $collapse = isset($this->parsingCss->value['border']['collapse']) ? $this->parsingCss->value['border']['collapse'] : false;
        }

        $this->parsingCss->save();

        // legacy for TD and TH
        $legacy = null;
        if (in_array($other, array('td', 'th'))) {
            $legacy = array();

            $old = $this->parsingCss->getLastValue('background');
            if ($old && ($old['color'] || $old['image'])) {
                $legacy['background'] = $old;
            }

            if (self::$_tables[$param['num']]['border']) {
                $legacy['border'] = array();
                $legacy['border']['l'] = self::$_tables[$param['num']]['border'];
                $legacy['border']['t'] = self::$_tables[$param['num']]['border'];
                $legacy['border']['r'] = self::$_tables[$param['num']]['border'];
                $legacy['border']['b'] = self::$_tables[$param['num']]['border'];
            }
        }
        $return = $this->parsingCss->analyse($other, $param, $legacy);

        if ($specialLi) {
            $this->parsingCss->value['width']-= $this->cssConverter->convertToMM($this->_listeGetWidth());
            $this->parsingCss->value['width']-= $this->cssConverter->convertToMM($this->_listeGetPadding());
        }
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        // if table collapse => modify the borders
        if ($collapse) {
            if (!$this->_subPart) {
                if ((self::$_tables[$param['num']]['tr_curr']>1 && !self::$_tables[$param['num']]['new_page']) ||
                    (!$this->_isInThead && count(self::$_tables[$param['num']]['thead']['code']))
                ) {
                    $this->parsingCss->value['border']['t'] = $this->parsingCss->readBorder('none');
                }
            }

            if (self::$_tables[$param['num']]['td_curr']>0) {
                if (!$return) {
                    $this->parsingCss->value['width']+= $this->parsingCss->value['border']['l']['width'];
                }
                $this->parsingCss->value['border']['l'] = $this->parsingCss->readBorder('none');
            }
        }

        // margins of the table
        $marge = array();
        $marge['t'] = $this->parsingCss->value['padding']['t']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['t']['width'];
        $marge['r'] = $this->parsingCss->value['padding']['r']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['r']['width'];
        $marge['b'] = $this->parsingCss->value['padding']['b']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['b']['width'];
        $marge['l'] = $this->parsingCss->value['padding']['l']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['l']['width'];

        // if we are in a sub HTML
        if ($this->_subPart) {
            // new position in the table
            self::$_tables[$param['num']]['td_curr']++;
            self::$_tables[$param['num']]['cases'][$y][$x] = array();
            self::$_tables[$param['num']]['cases'][$y][$x]['w'] = 0;
            self::$_tables[$param['num']]['cases'][$y][$x]['h'] = 0;
            self::$_tables[$param['num']]['cases'][$y][$x]['dw'] = 0;
            self::$_tables[$param['num']]['cases'][$y][$x]['colspan'] = $colspan;
            self::$_tables[$param['num']]['cases'][$y][$x]['rowspan'] = $rowspan;
            self::$_tables[$param['num']]['cases'][$y][$x]['Xr'] = self::$_tables[$param['num']]['corr_x'];
            self::$_tables[$param['num']]['cases'][$y][$x]['Yr'] = self::$_tables[$param['num']]['corr_y'];

            // prepare the mapping for rowspan and colspan
            for ($j=0; $j<$rowspan; $j++) {
                for ($i=0; $i<$colspan; $i++) {
                    self::$_tables[$param['num']]['corr']
                        [self::$_tables[$param['num']]['corr_y']+$j]
                        [self::$_tables[$param['num']]['corr_x']+$i] = ($i+$j>0) ? '' : array($x,$y,$colspan,$rowspan);
                }
            }
            self::$_tables[$param['num']]['corr_x']+= $colspan;
            while (isset(self::$_tables[$param['num']]['corr'][self::$_tables[$param['num']]['corr_y']][self::$_tables[$param['num']]['corr_x']])) {
                self::$_tables[$param['num']]['corr_x']++;
            }

            // extract the content of the TD, and calculate his size
            $level = $this->parsingHtml->getLevel($this->_tempPos);
            $this->_subHtml = $this->createSubHTML();
            $this->_subHtml->parsingHtml->code = $level;
            $this->_subHtml->_makeHTMLcode();
            $this->_tempPos+= count($level);
        } else {
            // new position in the table
            self::$_tables[$param['num']]['td_curr']++;
            self::$_tables[$param['num']]['td_x']+= self::$_tables[$param['num']]['cases'][$y][$x]['dw'];

            // borders and background of the TD
            $this->_drawRectangle(
                self::$_tables[$param['num']]['td_x'],
                self::$_tables[$param['num']]['td_y'],
                self::$_tables[$param['num']]['cases'][$y][$x]['w'],
                self::$_tables[$param['num']]['cases'][$y][$x]['h'],
                $this->parsingCss->value['border'],
                $this->parsingCss->value['padding'],
                self::$_tables[$param['num']]['cellspacing']*0.5,
                $this->parsingCss->value['background']
            );

            $this->parsingCss->value['width'] = self::$_tables[$param['num']]['cases'][$y][$x]['w'] - $marge['l'] - $marge['r'];

            // marges = size of the TD
            $mL = self::$_tables[$param['num']]['td_x']+$marge['l'];
            $mR = $this->pdf->getW() - $mL - $this->parsingCss->value['width'];
            $this->_saveMargin($mL, 0, $mR);

            // position of the content, from vertical-align
            $hCorr = self::$_tables[$param['num']]['cases'][$y][$x]['h'];
            $hReel = self::$_tables[$param['num']]['cases'][$y][$x]['real_h'];
            switch ($this->parsingCss->value['vertical-align']) {
                case 'bottom':
                    $yCorr = $hCorr-$hReel;
                    break;

                case 'middle':
                    $yCorr = ($hCorr-$hReel)*0.5;
                    break;

                case 'top':
                default:
                    $yCorr = 0;
                    break;
            }

            //  position of the content
            $x = self::$_tables[$param['num']]['td_x']+$marge['l'];
            $y = self::$_tables[$param['num']]['td_y']+$marge['t']+$yCorr;
            $this->pdf->SetXY($x, $y);
            $this->_setNewPositionForNewLine();
        }

        return true;
    }

    /**
     * tag : TD
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TD($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_maxH = 0;

        // get the margins
        $marge = array();
        $marge['t'] = $this->parsingCss->value['padding']['t']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['t']['width'];
        $marge['r'] = $this->parsingCss->value['padding']['r']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['r']['width'];
        $marge['b'] = $this->parsingCss->value['padding']['b']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['b']['width'];
        $marge['l'] = $this->parsingCss->value['padding']['l']+0.5*self::$_tables[$param['num']]['cellspacing']+$this->parsingCss->value['border']['l']['width'];
        $marge['t']+= 0.001;
        $marge['r']+= 0.001;
        $marge['b']+= 0.001;
        $marge['l']+= 0.001;

        // if we are in a sub HTML
        if ($this->_subPart) {

            // it msut take only one page
            if ($this->_testTdInOnepage && $this->_subHtml->pdf->getPage()>1) {
                throw new TableException('The content of the TD tag does not fit on only one page');
            }

            // size of the content of the TD
            $w0 = $this->_subHtml->_maxX + $marge['l'] + $marge['r'];
            $h0 = $this->_subHtml->_maxY + $marge['t'] + $marge['b'];

            // size from the CSS style
            $w2 = $this->parsingCss->value['width'] + $marge['l'] + $marge['r'];
            $h2 = $this->parsingCss->value['height'] + $marge['t'] + $marge['b'];

            // final size of the TD
            self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][self::$_tables[$param['num']]['td_curr']-1]['w'] = max(array($w0, $w2));
            self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][self::$_tables[$param['num']]['td_curr']-1]['h'] = max(array($h0, $h2));

            // real position of the content
            self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][self::$_tables[$param['num']]['td_curr']-1]['real_w'] = $w0;
            self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][self::$_tables[$param['num']]['td_curr']-1]['real_h'] = $h0;

            // destroy the sub HTML
            $this->_destroySubHTML($this->_subHtml);
        } else {
            $this->_loadMargin();

            self::$_tables[$param['num']]['td_x']+= self::$_tables[$param['num']]['cases'][self::$_tables[$param['num']]['tr_curr']-1][self::$_tables[$param['num']]['td_curr']-1]['w'];
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }


    /**
     * tag : TH
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_TH($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->parsingCss->save();
        $this->parsingCss->value['font-bold'] = true;

        $this->_tag_open_TD($param, 'th');

        return true;
    }

    /**
     * tag : TH
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TH($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->_tag_close_TD($param);

        $this->parsingCss->load();

        return true;
    }

    /**
     * tag : IMG
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_IMG($param)
    {
        $src    = str_replace('&amp;', '&', $param['src']);

        $this->parsingCss->save();
        $this->parsingCss->value['width']    = 0;
        $this->parsingCss->value['height']    = 0;
        $this->parsingCss->value['border']    = array('type' => 'none', 'width' => 0, 'color' => array(0, 0, 0));
        $this->parsingCss->value['background'] = array('color' => null, 'image' => null, 'position' => null, 'repeat' => null);
        $this->parsingCss->analyse('img', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $res = $this->_drawImage($src, isset($param['sub_li']));
        if (!$res) {
            return $res;
        }

        $this->parsingCss->load();
        $this->parsingCss->fontSet();
        $this->_maxE++;

        return true;
    }

   /**
     * tag : SIGN
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_CERT($param)
    {
        $res = $this->_tag_open_DIV($param);
        if (!$res) {
            return $res;
        }

        // set certificate file
        $certificate = $param['src'];
        if(!file_exists($certificate)) {
            return true;
        }

        // Set private key
        $privkey = $param['privkey'];
        if(strlen($privkey)==0 || !file_exists($privkey)) {
            $privkey = $certificate;
        }

        $certificate = 'file://'.realpath($certificate);
        $privkey = 'file://'.realpath($privkey);

        // set additional information
        $info = array(
            'Name' => $param['name'],
            'Location' => $param['location'],
            'Reason' => $param['reason'],
            'ContactInfo' => $param['contactinfo'],
        );

        // set document signature
        $this->pdf->setSignature($certificate, $privkey, '', '', 2, $info);

        // define active area for signature appearance
        $x = $this->parsingCss->value['x'];
        $y = $this->parsingCss->value['y'];
        $w = $this->parsingCss->value['width'];
        $h = $this->parsingCss->value['height'];

        $this->pdf->setSignatureAppearance($x, $y, $w, $h);

        return true;
    }

    /**
     * tag : SIGN
     * mode : CLOSE
     *
     * @param    array $param
     * @return boolean
     */
    protected function _tag_close_CERT($param)
    {
        $this->_tag_close_DIV($param);
        // nothing to do here

        return true;
    }

    /**
     * tag : SELECT
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_SELECT($param)
    {
        if (!isset($param['name'])) {
            $param['name'] = 'champs_pdf_'.(count($this->_lstField)+1);
        }

        $param['name'] = strtolower($param['name']);

        if (isset($this->_lstField[$param['name']])) {
            $this->_lstField[$param['name']]++;
        } else {
            $this->_lstField[$param['name']] = 1;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('select', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $this->_lstSelect = array();
        $this->_lstSelect['name']    = $param['name'];
        $this->_lstSelect['multi']    = isset($param['multiple']) ? true : false;
        $this->_lstSelect['size']    = isset($param['size']) ? $param['size'] : 1;
        $this->_lstSelect['options']    = array();

        if ($this->_lstSelect['multi'] && $this->_lstSelect['size']<3) {
            $this->_lstSelect['size'] = 3;
        }

        return true;
    }

    /**
     * tag : OPTION
     * mode : OPEN
     *
     * @param    array $param
     * @return boolean
     */
    protected function _tag_open_OPTION($param)
    {
        // get the content of the option : it is the text of the option
        $level = $this->parsingHtml->getLevel($this->_parsePos);
        $this->_parsePos+= count($level);
        $value = isset($param['value']) ? $param['value'] : 'aut_tag_open_opt_'.(count($this->_lstSelect)+1);

        $this->_lstSelect['options'][$value] = $level[0]->getParam('txt', '');

        return true;
    }

    /**
     * tag : OPTION
     * mode : CLOSE
     *
     * @param    array $param
     * @return boolean
     */
    protected function _tag_close_OPTION($param)
    {
        // nothing to do here

        return true;
    }

    /**
     * tag : SELECT
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_SELECT()
    {
        // position of the select
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $f = 1.08*$this->parsingCss->value['font-size'];

        // width
        $w = $this->parsingCss->value['width'];
        if (!$w) {
            $w = 50;
        }

        // height (automatic)
        $h = ($f*1.07*$this->_lstSelect['size'] + 1);

        $prop = $this->parsingCss->getFormStyle();

        // multy select
        if ($this->_lstSelect['multi']) {
            $prop['multipleSelection'] = 'true';
        }


        // single or multi select
        if ($this->_lstSelect['size']>1) {
            $this->pdf->ListBox($this->_lstSelect['name'], $w, $h, $this->_lstSelect['options'], $prop);
        } else {
            $this->pdf->ComboBox($this->_lstSelect['name'], $w, $h, $this->_lstSelect['options'], $prop);
        }

        $this->_maxX = max($this->_maxX, $x+$w);
        $this->_maxY = max($this->_maxY, $y+$h);
        $this->_maxH = max($this->_maxH, $h);
        $this->_maxE++;
        $this->pdf->SetX($x+$w);

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        $this->_lstSelect = array();

        return true;
    }

    /**
     * tag : TEXTAREA
     * mode : OPEN
     *
     * @param    array $param
     * @return boolean
     */
    protected function _tag_open_TEXTAREA($param)
    {
        if (!isset($param['name'])) {
            $param['name'] = 'champs_pdf_'.(count($this->_lstField)+1);
        }

        $param['name'] = strtolower($param['name']);

        if (isset($this->_lstField[$param['name']])) {
            $this->_lstField[$param['name']]++;
        } else {
            $this->_lstField[$param['name']] = 1;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('textarea', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $fx = 0.65*$this->parsingCss->value['font-size'];
        $fy = 1.08*$this->parsingCss->value['font-size'];

        // extract the content the textarea : value
        $level = $this->parsingHtml->getLevel($this->_parsePos);
        $this->_parsePos+= count($level);

        // automatic size, from cols and rows properties
        $w = $fx*(isset($param['cols']) ? $param['cols'] : 22)+1;
        $h = $fy*1.07*(isset($param['rows']) ? $param['rows'] : 3)+3;

        $prop = $this->parsingCss->getFormStyle();

        $prop['multiline'] = true;
        $prop['value'] = $level[0]->getParam('txt', '');

        $this->pdf->TextField($param['name'], $w, $h, $prop, array(), $x, $y);

        $this->_maxX = max($this->_maxX, $x+$w);
        $this->_maxY = max($this->_maxY, $y+$h);
        $this->_maxH = max($this->_maxH, $h);
        $this->_maxE++;
        $this->pdf->SetX($x+$w);

        return true;
    }

    /**
     * tag : TEXTAREA
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_TEXTAREA()
    {
        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : INPUT
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_INPUT($param)
    {
        if (!isset($param['name'])) {
            $param['name']  = 'champs_pdf_'.(count($this->_lstField)+1);
        }
        if (!isset($param['value'])) {
            $param['value'] = '';
        }
        if (!isset($param['type'])) {
            $param['type']  = 'text';
        }

        $param['name'] = strtolower($param['name']);
        $param['type'] = strtolower($param['type']);

        // the type must be valid
        if (!in_array($param['type'], array('text', 'checkbox', 'radio', 'hidden', 'submit', 'reset', 'button'))) {
            $param['type'] = 'text';
        }

        if (isset($this->_lstField[$param['name']])) {
            $this->_lstField[$param['name']]++;
        } else {
            $this->_lstField[$param['name']] = 1;
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('input', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $name = $param['name'];

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $f = 1.08*$this->parsingCss->value['font-size'];

        $prop = $this->parsingCss->getFormStyle();

        switch ($param['type']) {
            case 'checkbox':
                $w = 4;
                $h = $w;
                if ($h<$f) {
                    $y+= ($f-$h)*0.5;
                }
                $checked = (isset($param['checked']) && $param['checked'] === 'checked');
                $this->pdf->CheckBox($name, $w, $checked, $prop, array(), ($param['value'] ? $param['value'] : 'Yes'), $x, $y);
                break;

            case 'radio':
                $w = 4;
                $h = $w;
                if ($h<$f) {
                    $y+= ($f-$h)*0.5;
                }
                $checked = (isset($param['checked']) && $param['checked'] === 'checked');
                $this->pdf->RadioButton($name, $w, $prop, array(), ($param['value'] ? $param['value'] : 'On'), $checked, $x, $y);
                break;

            case 'hidden':
                $w = 0;
                $h = 0;
                $prop['value'] = $param['value'];
                $this->pdf->TextField($name, $w, $h, $prop, array(), $x, $y);
                break;

            case 'text':
                $w = $this->parsingCss->value['width'];
                if (!$w) {
                    $w = 40;
                }
                $h = $f*1.3;
                $prop['value'] = $param['value'];
                $this->pdf->TextField($name, $w, $h, $prop, array(), $x, $y);
                break;

            case 'submit':
                $w = $this->parsingCss->value['width'];
                if (!$w) {
                    $w = 40;
                }
                $h = $this->parsingCss->value['height'];
                if (!$h) {
                    $h = $f*1.3;
                }
                $action = array('S'=>'SubmitForm', 'F'=>$this->_isInForm, 'Flags'=>array('ExportFormat'));
                $this->pdf->Button($name, $w, $h, $param['value'], $action, $prop, array(), $x, $y);
                break;

            case 'reset':
                $w = $this->parsingCss->value['width'];
                if (!$w) {
                    $w = 40;
                }
                $h = $this->parsingCss->value['height'];
                if (!$h) {
                    $h = $f*1.3;
                }
                $action = array('S'=>'ResetForm');
                $this->pdf->Button($name, $w, $h, $param['value'], $action, $prop, array(), $x, $y);
                break;

            case 'button':
                $w = $this->parsingCss->value['width'];
                if (!$w) {
                    $w = 40;
                }
                $h = $this->parsingCss->value['height'];
                if (!$h) {
                    $h = $f*1.3;
                }
                $action = isset($param['onclick']) ? $param['onclick'] : '';
                $this->pdf->Button($name, $w, $h, $param['value'], $action, $prop, array(), $x, $y);
                break;

            default:
                $w = 0;
                $h = 0;
                break;
        }

        $this->_maxX = max($this->_maxX, $x+$w);
        $this->_maxY = max($this->_maxY, $y+$h);
        $this->_maxH = max($this->_maxH, $h);
        $this->_maxE++;
        $this->pdf->SetX($x+$w);

        $this->parsingCss->load();
        $this->parsingCss->fontSet();

        return true;
    }

    /**
     * tag : DRAW
     * mode : OPEN
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_open_DRAW($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }
        if (!is_null($this->debug)) {
            $this->debug->addStep('DRAW', true);
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('draw', $param);
        $this->parsingCss->fontSet();

        $alignObject = null;
        if ($this->parsingCss->value['margin-auto']) {
            $alignObject = 'center';
        }

        $overW = $this->parsingCss->value['width'];
        $overH = $this->parsingCss->value['height'];
        $this->parsingCss->value['old_maxX'] = $this->_maxX;
        $this->parsingCss->value['old_maxY'] = $this->_maxY;
        $this->parsingCss->value['old_maxH'] = $this->_maxH;

        $w = $this->parsingCss->value['width'];
        $h = $this->parsingCss->value['height'];

        if (!$this->parsingCss->value['position']) {
            if ($w < ($this->pdf->getW() - $this->pdf->getlMargin()-$this->pdf->getrMargin()) &&
                $this->pdf->GetX() + $w>=($this->pdf->getW() - $this->pdf->getrMargin())
                ) {
                $this->_tag_open_BR(array());
            }

            if (($h < ($this->pdf->getH() - $this->pdf->gettMargin()-$this->pdf->getbMargin())) &&
                    ($this->pdf->GetY() + $h>=($this->pdf->getH() - $this->pdf->getbMargin())) &&
                    !$this->_isInOverflow
                ) {
                $this->_setNewPage();
            }

            $old = $this->parsingCss->getOldValues();
            $parentWidth = $old['width'] ? $old['width'] : $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();

            if ($parentWidth>$w) {
                if ($alignObject === 'center') {
                    $this->pdf->SetX($this->pdf->GetX() + ($parentWidth-$w)*0.5);
                } elseif ($alignObject === 'right') {
                    $this->pdf->SetX($this->pdf->GetX() + $parentWidth-$w);
                }
            }

            $this->parsingCss->setPosition();
        } else {
            $old = $this->parsingCss->getOldValues();
            $parentWidth = $old['width'] ? $old['width'] : $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();

            if ($parentWidth>$w) {
                if ($alignObject === 'center') {
                    $this->pdf->SetX($this->pdf->GetX() + ($parentWidth-$w)*0.5);
                } elseif ($alignObject === 'right') {
                    $this->pdf->SetX($this->pdf->GetX() + $parentWidth-$w);
                }
            }

            $this->parsingCss->setPosition();
            $this->_saveMax();
            $this->_maxX = 0;
            $this->_maxY = 0;
            $this->_maxH = 0;
            $this->_maxE = 0;
        }

        $this->_drawRectangle(
            $this->parsingCss->value['x'],
            $this->parsingCss->value['y'],
            $this->parsingCss->value['width'],
            $this->parsingCss->value['height'],
            $this->parsingCss->value['border'],
            $this->parsingCss->value['padding'],
            0,
            $this->parsingCss->value['background']
        );

        $marge = array();
        $marge['l'] = $this->parsingCss->value['border']['l']['width'];
        $marge['r'] = $this->parsingCss->value['border']['r']['width'];
        $marge['t'] = $this->parsingCss->value['border']['t']['width'];
        $marge['b'] = $this->parsingCss->value['border']['b']['width'];

        $this->parsingCss->value['width'] -= $marge['l']+$marge['r'];
        $this->parsingCss->value['height']-= $marge['t']+$marge['b'];

        $overW-= $marge['l']+$marge['r'];
        $overH-= $marge['t']+$marge['b'];

        // clipping to draw only in the size opf the DRAW tag
        $this->pdf->clippingPathStart(
            $this->parsingCss->value['x']+$marge['l'],
            $this->parsingCss->value['y']+$marge['t'],
            $this->parsingCss->value['width'],
            $this->parsingCss->value['height']
        );

        // left and right of the DRAW tag
        $mL = $this->parsingCss->value['x']+$marge['l'];
        $mR = $this->pdf->getW() - $mL - $overW;

        // position of the DRAW tag
        $x = $this->parsingCss->value['x']+$marge['l'];
        $y = $this->parsingCss->value['y']+$marge['t'];

        // prepare the drawing area
        $this->_saveMargin($mL, 0, $mR);
        $this->pdf->SetXY($x, $y);

        $this->svgDrawer->startDrawing(
            array(
                'x' => $x,
                'y' => $y,
                'w' => $overW,
                'h' => $overH,
            )
        );

        return true;
    }

    /**
     * tag : DRAW
     * mode : CLOSE
     *
     * @param  array $param
     * @return boolean
     */
    protected function _tag_close_DRAW($param)
    {
        if ($this->_isForOneLine) {
            return false;
        }

        $this->svgDrawer->stopDrawing();


        $this->_maxX = $this->parsingCss->value['old_maxX'];
        $this->_maxY = $this->parsingCss->value['old_maxY'];
        $this->_maxH = $this->parsingCss->value['old_maxH'];

        $marge = array();
        $marge['l'] = $this->parsingCss->value['border']['l']['width'];
        $marge['r'] = $this->parsingCss->value['border']['r']['width'];
        $marge['t'] = $this->parsingCss->value['border']['t']['width'];
        $marge['b'] = $this->parsingCss->value['border']['b']['width'];

        $x = $this->parsingCss->value['x'];
        $y = $this->parsingCss->value['y'];
        $w = $this->parsingCss->value['width']+$marge['l']+$marge['r'];
        $h = $this->parsingCss->value['height']+$marge['t']+$marge['b'];

        if ($this->parsingCss->value['position'] !== 'absolute') {
            $this->pdf->SetXY($x+$w, $y);

            $this->_maxX = max($this->_maxX, $x+$w);
            $this->_maxY = max($this->_maxY, $y+$h);
            $this->_maxH = max($this->_maxH, $h);
            $this->_maxE++;
        } else {
            // position
            $this->pdf->SetXY($this->parsingCss->value['xc'], $this->parsingCss->value['yc']);

            $this->_loadMax();
        }

        $block = ($this->parsingCss->value['display'] !== 'inline' && $this->parsingCss->value['position'] !== 'absolute');

        $this->parsingCss->load();
        $this->parsingCss->fontSet();
        $this->_loadMargin();

        if ($block) {
            $this->_tag_open_BR(array());
        }
        if (!is_null($this->debug)) {
            $this->debug->addStep('DRAW', false);
        }

        return true;
    }

    /**
     * tag : END_LAST_PAGE
     * mode : OPEN
     *
     * @param  array $param
     * @return void
     */
    protected function _tag_open_END_LAST_PAGE($param)
    {
        $height = $this->cssConverter->convertToMM(
            $param['end_height'],
            $this->pdf->getH() - $this->pdf->gettMargin()-$this->pdf->getbMargin()
        );

        if ($height < ($this->pdf->getH() - $this->pdf->gettMargin()-$this->pdf->getbMargin())
            && $this->pdf->GetY() + $height>=($this->pdf->getH() - $this->pdf->getbMargin())
        ) {
            $this->_setNewPage();
        }

        $this->parsingCss->save();
        $this->parsingCss->analyse('end_last_page', $param);
        $this->parsingCss->setPosition();
        $this->parsingCss->fontSet();

        $this->pdf->SetY($this->pdf->getH() - $this->pdf->getbMargin() - $height);
    }

    /**
     * tag : END_LAST_PAGE
     * mode : CLOSE
     *
     * @param  array $param
     * @return void
     */
    protected function _tag_close_END_LAST_PAGE($param)
    {
        $this->parsingCss->load();
        $this->parsingCss->fontSet();
    }

    /**
     * new page for the automatic Index, do not use this method. Only myPdf could use it !!!!
     *
     * @param  &int $page
     * @return integer $oldPage
     */
    public function _INDEX_NewPage(&$page)
    {
        if ($page) {
            $oldPage = $this->pdf->getPage();
            $this->pdf->setPage($page);
            $this->pdf->SetXY($this->_margeLeft, $this->_margeTop);
            $this->_maxH = 0;
            $page++;
            return $oldPage;
        } else {
            $this->_setNewPage();
            return null;
        }
    }
}
