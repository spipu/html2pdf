<?php

namespace Spipu\Html2Pdf\Css;

use Spipu\Html2Pdf\Css\Parser\ClassSelectorParser;
use Spipu\Html2Pdf\Css\Parser\ChildSelectorParser;
use Spipu\Html2Pdf\Css\Parser\IdSelectorParser;
use Spipu\Html2Pdf\Css\Parser\SelectorParserInterface;

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
