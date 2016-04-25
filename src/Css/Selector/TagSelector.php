<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

/**
 * Class TagSelector
 */
class TagSelector extends AbstractSelector
{
    /**
     * {@inheritDoc}
     */
    public function validate(NodeInterface $node)
    {
        if ($this->getText() == $node->getName()) {
            return $node;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'tag';
    }
}
