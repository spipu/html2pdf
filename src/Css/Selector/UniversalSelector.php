<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

/**
 * Class UniversalSelector
 */
class UniversalSelector extends AbstractSelector
{
    public function validate(NodeInterface $node)
    {
        return $node;
    }

    public function getName()
    {
        return 'universal';
    }
}
