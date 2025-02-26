<?php
/**
 * Html2Pdf Library - Security
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Security;

use Spipu\Html2Pdf\Exception\HtmlParsingException;

interface SecurityInterface
{
    /**
     * @param string $path
     * @return void
     * @throws HtmlParsingException
     */
    public function checkValidPath(string $path): void;
}
