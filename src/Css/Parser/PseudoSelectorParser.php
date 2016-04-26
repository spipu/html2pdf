<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\PseudoSelector;

/**
 * Class PseudoSelectorParser
 */
class PseudoSelectorParser implements SelectorParserInterface
{
    public function match($partial)
    {
        if (preg_match('/^(:[\w-]+(\([^)]+\))?)/', $partial, $matches)) {
            return new PseudoSelector($matches[1]);
        }
        return false;
    }
}
