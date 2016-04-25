<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\TagSelector;

/**
 * Class TagSelectorParser
 */
class TagSelectorParser implements SelectorParserInterface
{
    /**
     * {@inheritDoc}
     */
    public function match($partial)
    {
        if (preg_match('/^(\w+)/', $partial, $matches)) {
            return new TagSelector($matches[1]);
        }
        return false;
    }
}
