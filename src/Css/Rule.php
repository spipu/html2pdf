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

    public function __construct($text, $selectors)
    {
        $this->text = trim($text);
        $this->selectors = $selectors;
    }

    public function match(NodeInterface $node)
    {
        foreach ($this->getSelectors() as $selector) {
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
}
