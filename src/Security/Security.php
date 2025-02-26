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

class Security implements SecurityInterface
{
    protected $authorizedSchemes = ['file', 'http', 'https'];

    /**
     * @param string $path
     * @return void
     * @throws HtmlParsingException
     */
    public function checkValidPath(string $path): void
    {
        $path = trim(strtolower($path));
        $scheme = parse_url($path, PHP_URL_SCHEME);

        if ($scheme === null) {
            return;
        }

        if (in_array($scheme, $this->authorizedSchemes)) {
            return;
        }

        if (strlen($scheme) === 1 && preg_match('/^[a-z]$/i', $scheme)) {
            return;
        }

        throw new HtmlParsingException('Unauthorized path scheme');
    }
}
