<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\ClassSelector;

class ClassSelectorParser implements SelectorParserInterface
{
    public function match($partial)
    {
        if (preg_match('/^(\.\w+)/', $partial, $matches)) {
            return new ClassSelector($matches[1]);
        }
        return false;
    }
}
