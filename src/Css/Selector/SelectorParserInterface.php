<?php

namespace Spipu\Html2Pdf\Css\Selector;

interface SelectorParserInterface
{
    /**
     * @param $partial
     *
     * @return SelectorInterface
     */
    public function match($partial);

}
