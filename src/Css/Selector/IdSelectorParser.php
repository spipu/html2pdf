<?php

namespace Spipu\Html2Pdf\Css\Selector;

class IdSelectorParser implements SelectorParserInterface
{
    public function match($partial)
    {
        if (preg_match('/^(#\w+)/', $partial, $matches)) {
            return new IdSelector($matches[1]);
        }
        return false;
    }
}
