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
    public function open($properties)
    {
        $this->openSvg($properties);
        $this->drawSvg($properties);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function close($properties)
    {
        $this->closeSvg();

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function drawSvg($properties)
    {
        $styles = $this->parsingCss->getSvgStyle($this->getName(), $properties);
        $this->pdf->svgSetStyle($styles);
    }
}
