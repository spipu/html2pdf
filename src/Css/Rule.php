<?php 

namespace Spipu\Html2Pdf\Css;

use Spipu\Html2Pdf\Css\Selector\SelectorInterface;
use Spipu\Html2Pdf\Html\NodeInterface;

/**
 * Class Rule
 */
class Rule
{
    private $text;

    private $selectors;

    private $styles;

    public function __construct($text, $selectors, $styles = array())
    {
        $this->text = trim($text);
        $this->selectors = $selectors;
        $this->styles = $styles;
    }

    public function match(NodeInterface $node)
    {
        foreach (array_reverse($this->getSelectors()) as $selector) {
            $node = $selector->validate($node);
            if (!$node) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return SelectorInterface[]
     */
    public function getSelectors()
    {
        return $this->selectors;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
