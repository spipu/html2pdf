<?php

namespace Spipu\Html2Pdf\Css;

use Spipu\Html2Pdf\Css\Selector\ClassSelectorParser;
use Spipu\Html2Pdf\Css\Selector\ChildSelectorParser;
use Spipu\Html2Pdf\Css\Selector\IdSelectorParser;
use Spipu\Html2Pdf\Css\Selector\SelectorParserInterface;

class SelectorProvider
{
    /**
     * @return SelectorParserInterface[]
     */
    public function getParsers()
    {
        return array(
            new IdSelectorParser(),
            new ClassSelectorParser(),
            new ChildSelectorParser(),
        );
    }
}
