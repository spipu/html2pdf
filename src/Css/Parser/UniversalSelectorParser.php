<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\UniversalSelector;

/**
 * Class UniversalSelectorParser
 */
class UniversalSelectorParser implements SelectorParserInterface
{
    /**
     * {@inheritDoc}
     */
    public function match($partial)
    {
        if (strpos($partial, '*') === 0) {
            return new UniversalSelector('*');
        }

        return false;
    }
}
