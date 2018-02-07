<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

use Spipu\Html2Pdf\Exception\HtmlParsingException;
use Spipu\Html2Pdf\SvgDrawer;

/**
 * Abstract Default Tag
 * used by all the svg tags
 */
abstract class AbstractSvgTag extends AbstractTag
{
    /**
     * @var SvgDrawer
     */
    protected $svgDrawer;

    /**
     * AbstractSvgTag constructor.
     *
     * @param SvgDrawer $svgDrawer
     */
    public function __construct(SvgDrawer $svgDrawer)
    {
        parent::__construct();

        $this->svgDrawer = $svgDrawer;
    }

    /**
     * @inheritdoc
     */
    public function open($properties)
    {
        $this->openSvg($properties);
        $this->drawSvg($properties);
        $this->closeSvg();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function close($properties)
    {
        return true;
    }

    /**
     * Open the SVG tag
     *
     * @param array $properties
     * @throws HtmlParsingException
     */
    protected function openSvg($properties)
    {
        if (!$this->svgDrawer->isDrawing()) {
            $e = new HtmlParsingException('The asked ['.$this->getName().'] tag is not in a [DRAW] tag');
            $e->setInvalidTag($this->getName());
            throw $e;
        }

        $transform = null;
        if (array_key_exists('transform', $properties)) {
            $transform = $this->svgDrawer->prepareTransform($properties['transform']);
        }

        $this->pdf->doTransform($transform);
        $this->parsingCss->save();
    }

    /**
     * Close the SVG tag
     */
    protected function closeSvg()
    {
        $this->pdf->undoTransform();

        $this->parsingCss->load();
    }

    /**
     * Draw the SVG tag
     *
     * @param array $properties
     *
     * @return void
     */
    abstract protected function drawSvg($properties);
}
