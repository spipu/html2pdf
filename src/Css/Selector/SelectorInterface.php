<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

/**
 * Interface SelectorInterface
 */
interface SelectorInterface
{
    /**
     * The substring in the rule this selector corresponds to
     *
     * @return string
     */
    public function getText();

    /**
     * The name of the selector
     *
     * @return string
     */
    public function getName();

    /**
     * @param SelectorInterface $previous
     *
     * @return mixed
     */
    public function setPrevious(SelectorInterface $previous = null);

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface|false
     */
    public function validate(NodeInterface $node);
}
