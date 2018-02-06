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
namespace Spipu\Html2Pdf\Tag\Svg;

use Spipu\Html2Pdf\Tag\AbstractSvgTag;

/**
 * Tag G
 */
class G extends AbstractSvgTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'g';
    }

    /**
     * @inheritdoc
     */
    protected function draw($properties)
    {
        $this->pdf->doTransform(isset($properties['transform']) ? $this->svgDrawer->prepareTransform($properties['transform']) : null);
        $this->parsingCss->save();
        $styles = $this->parsingCss->getSvgStyle('path', $properties);
        $style = $this->pdf->svgSetStyle($styles);
    }

    /**
     * @inheritdoc
     */
    public function close($properties)
    {
        $this->pdf->undoTransform();
        $this->parsingCss->load();

        return parent::close($properties);
    }
}
