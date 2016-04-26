<?php

namespace Spipu\Html2Pdf\Css;

use Spipu\Html2Pdf\Css\Parser\ClassSelectorParser;
use Spipu\Html2Pdf\Css\Parser\ChildSelectorParser;
use Spipu\Html2Pdf\Css\Parser\IdSelectorParser;
use Spipu\Html2Pdf\Css\Parser\SelectorParserInterface;
use Spipu\Html2Pdf\Css\Parser\TagSelectorParser;
use Spipu\Html2Pdf\Css\Parser\UniversalSelectorParser;

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
            new TagSelectorParser(),
            new UniversalSelectorParser(),
        );
    }
}
