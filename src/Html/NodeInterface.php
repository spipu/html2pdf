<?php 

namespace Spipu\Html2Pdf\Html;

/**
 * Interface NodeInterface
 */
interface NodeInterface
{
    /**
     * @return NodeInterface[]
     */
    public function getChildren();

    /**
     * @return NodeInterface
     */
    public function getParent();

    /**
     * @return array
     */
    public function getParams();

    /**
     * @return string
     */
    public function getName();
}
