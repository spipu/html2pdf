<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Extension\Core;

use Spipu\Html2Pdf\Extension\AbstractExtension;
use Spipu\Html2Pdf\SvgDrawer;
use Spipu\Html2Pdf\Tag\Svg;

/**
 * Class SvgExtension
 */
class SvgExtension extends AbstractExtension
{
    /**
     * @var SvgDrawer
     */
    private $svgDrawer;

    /**
     * SvgExtension constructor.
     *
     * @param SvgDrawer $svgDrawer
     */
    public function __construct(SvgDrawer $svgDrawer)
    {
        $this->svgDrawer = $svgDrawer;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_svg';
    }

    /**
     * @inheritdoc
     */
    protected function initTags()
    {
        return array(
            new Svg\Circle($this->svgDrawer),
            new Svg\Ellipse($this->svgDrawer),
            new Svg\G($this->svgDrawer),
            new Svg\Line($this->svgDrawer),
            new Svg\Path($this->svgDrawer),
            new Svg\Polygon($this->svgDrawer),
            new Svg\Polyline($this->svgDrawer),
            new Svg\Rect($this->svgDrawer),
        );
    }
}
