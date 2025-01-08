<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
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
