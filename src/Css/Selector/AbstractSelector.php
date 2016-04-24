<?php

namespace Spipu\Html2Pdf\Css\Selector;

/**
 * Class AbstractSelector
 */
abstract class AbstractSelector implements SelectorInterface
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }
    public function getText()
    {
        return $this->text;
    }
}
