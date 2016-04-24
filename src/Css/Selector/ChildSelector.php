<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

class ChildSelector extends AbstractSelector
{
    public function validate(NodeInterface $node)
    {
        while ($parent = $node->getParent()) {
            if ($this->previous->validate($parent)) {
                return $parent;
            }
            $node = $parent;
        }

        return false;
    }
    public function getName()
    {
        return 'child';
    }
}
