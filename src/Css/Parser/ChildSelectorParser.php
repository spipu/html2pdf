<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\ChildSelector;

class ChildSelectorParser implements SelectorParserInterface
{
    public function match($partial)
    {
        if (preg_match('/^(\s+)/', $partial, $matches)) {
            return new ChildSelector($matches[1]);
        }
        return false;
    }
}
