<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\SelectorInterface;

/**
 * Interface SelectorParserInterface
 */
interface SelectorParserInterface
{
    /**
     * @param string $partial
     *
     * @return SelectorInterface
     */
    public function match($partial);

}
