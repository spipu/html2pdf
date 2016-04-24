<?php

namespace Spipu\Html2Pdf\Css\Selector;

class ClassSelectorParser implements SelectorParserInterface
{
    public function match($partial)
    {
        if (preg_match('/(\.\w+)$/', $partial, $matches)) {
            return new ClassSelector($matches[1]);
        }
        return false;
    }
}
