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
    /**
     * @var string[]
     */
    protected $allowedSchemes = ['file', 'http', 'https'];

    /**
     * @var string[]
     */
    protected $allowedHosts = [];

    /**
     * @param string $path
     * @return void
     * @throws HtmlParsingException
     */
    public function checkValidPath(string $path): void
    {
        $path = trim(strtolower($path));
        if (!$this->checkValidPathScheme($path)) {
            throw new HtmlParsingException('Unauthorized path scheme');
        }

        if (!$this->checkValidPathHost($path)) {
            throw new HtmlParsingException('Unauthorized path host on ' . $path . ' => ' . implode('|', $this->allowedHosts));
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function checkValidPathScheme(string $path): bool
    {
        $scheme = parse_url($path, PHP_URL_SCHEME);
        if ($scheme === null) {
            return true;
        }

        if (in_array($scheme, $this->allowedSchemes)) {
            return true;
        }

        // for local file on windows
        if (strlen($scheme) === 1 && preg_match('/^[a-z]$/i', $scheme)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function checkValidPathHost(string $path): bool
    {
        $scheme = parse_url($path, PHP_URL_SCHEME);
        $host = parse_url($path, PHP_URL_HOST);
        // If it is a local file => no host
        if (
            $scheme === null
            || $scheme === 'file'
            || (strlen($scheme) === 1 && preg_match('/^[a-z]$/i', $scheme))
        ) {
            return true;
        }
        return in_array($host, $this->allowedHosts);
    }

    /**
     * @param string $host
     * @return void
     */
    public function addAllowedHost(string $host): void
    {
        $host = trim(strtolower($host));
        if (!in_array($host, $this->allowedHosts)) {
            $this->allowedHosts[] = $host;
        }
    }
}
