<?php 

namespace Spipu\Html2Pdf\Html;

/**
 * Class Node
 */
class Node implements NodeInterface
{
    private $children;
    private $parent;
    private $params;

    /**
     * @param string $name
     * @param array  $params
     */
    public function __construct($name, $params)
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getParams()
    {
        return $this->params;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
