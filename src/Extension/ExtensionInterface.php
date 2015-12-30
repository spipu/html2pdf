<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Extension;

/**
 * Interface ExtensionInterface
 */
interface ExtensionInterface
{
    /**
     * Get the extension's name
     *
     * @return string
     */
    public function getName();

    /**
     * @return array()
     */
    public function getTags();
}
