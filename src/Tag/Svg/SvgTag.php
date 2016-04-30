<?php 

namespace Spipu\Html2Pdf\Tag\Svg;

use Spipu\Html2Pdf\SvgDrawer;
use Spipu\Html2Pdf\Tag\AbstractTag;

/**
 * Class SvgTag
 */
abstract class SvgTag extends AbstractTag implements SvgTagInterface
{
    /**
     * @var SvgDrawer
     */
    protected $svgDrawer;

    /**
     * @param SvgDrawer $drawer
     */
    public function setDrawer(SvgDrawer $drawer)
    {
        $this->svgDrawer = $drawer;
    }
}
