<?php

namespace Spipu\Html2Pdf\Css\Selector;

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
}
