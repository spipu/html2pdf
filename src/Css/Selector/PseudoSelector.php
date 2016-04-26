<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

/**
 * Class PseudoSelector
 */
class PseudoSelector extends AbstractSelector
{
    public function validate(NodeInterface $node)
    {
        // not supported
        return false;
    }

    public function getName()
    {
        return 'pseudo';
    }
}
