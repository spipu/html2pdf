<?php 

namespace Spipu\Html2Pdf\Tag\Svg;

use Spipu\Html2Pdf\SvgDrawer;

/**
 * Interface SvgTagInterface
 */
interface SvgTagInterface
{
    public function setDrawer(SvgDrawer $drawer);
}
