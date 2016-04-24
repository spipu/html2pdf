<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

class ClassSelector extends AbstractSelector
{
    public function validate(NodeInterface $node)
    {
        $params = $node->getParams();

        if (!isset($params['class'])) {
            return false;
        }

        if (array_search(substr($this->getText(), 1), explode(' ', $params['class']))) {
            return $node;
        }

        return false;
    }

    public function getName()
    {
        return 'class';
    }
}
