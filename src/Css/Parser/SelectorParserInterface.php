<?php

namespace Spipu\Html2Pdf\Css\Parser;

use Spipu\Html2Pdf\Css\Selector\SelectorInterface;

interface SelectorParserInterface
{
    /**
     * @param $partial
     *
     * @return SelectorInterface
     */
    public function match($partial);

}
