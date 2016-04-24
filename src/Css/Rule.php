<?php 

namespace Spipu\Html2Pdf\Css;

use Spipu\Html2Pdf\Css\Selector\SelectorInterface;

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

    /**
     * @return SelectorInterface[]
     */
    public function getSelectors()
    {
        return $this->selectors;
    }
}
