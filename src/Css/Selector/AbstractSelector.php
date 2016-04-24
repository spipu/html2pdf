<?php

namespace Spipu\Html2Pdf\Css\Selector;

/**
 * Class AbstractSelector
 */
abstract class AbstractSelector implements SelectorInterface
{
    private $text;

    /**
     * @var SelectorInterface
     */
    protected $previous;

    public function __construct($text)
    {
        $this->text = $text;
    }
    public function getText()
    {
        return $this->text;
    }

    public function setPrevious(SelectorInterface $previous = null)
    {
        $this->previous = $previous;
    }
}
