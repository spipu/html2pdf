<?php 

namespace Spipu\Html2Pdf\Extension;

use Spipu\Html2Pdf\SvgDrawer;
use Spipu\Html2Pdf\Tag\Svg\SvgTagInterface;

/**
 * Class SvgExtension
 */
class SvgExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    private $tagDefinitions = array();

    /**
     * @var SvgDrawer
     */
    private $drawer;

    /**
     * @param $drawer
     */
    public function __construct(SvgDrawer $drawer)
    {
        $this->drawer = $drawer;
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'svg';
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        if (empty($this->tagDefinitions)) {
            $this->tagDefinitions = array(
                new \Spipu\Html2Pdf\Tag\Svg\Circle(),
                new \Spipu\Html2Pdf\Tag\Svg\Ellipse(),
            );

            /** @var SvgTagInterface $tag */
            foreach ($this->tagDefinitions as $tag) {
                $tag->setDrawer($this->drawer);
            }
        }

        return $this->tagDefinitions;
    }
}
