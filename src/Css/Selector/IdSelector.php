<?php

namespace Spipu\Html2Pdf\Css\Selector;

use Spipu\Html2Pdf\Html\NodeInterface;

class IdSelector extends AbstractSelector
{
    public function validate(NodeInterface $node)
    {
        $params = $node->getParams();

        if (!isset($params['id'])) {
            return false;
        }

        if ($this->getText() == '#' . $params['id']) {
            return $node;
        }

        return false;
    }

    public function getName()
    {
        return 'id';
    }
}
