<?php
/**
 * Html2Pdf Library - Locale
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf;

use Spipu\Html2Pdf\Exception\LocaleException;

class Locale
{
    /**
     * code of the current used locale
     * @var string
     */
    static protected $code = null;

    /**
     * texts of the current used locale
     * @var array
     */
    static protected $list = array();

    /**
     * directory where locale files are
     * @var string
     */
    static protected $directory = null;

    /**
     * load the locale
     *
     * @param  string $code
     *
     * @return void
     * @throws LocaleException
     */
    public static function load($code)
    {
        if (self::$directory === null) {
            self::$directory = __DIR__ . '/locale/';
        }

        // must be in lower case
        $code = strtolower($code);

        // must be [a-z-0-9]
        if (!preg_match('/^([a-z0-9]+)$/isU', $code)) {
            $e = new LocaleException(
                'invalid language code'
            );
            $e->setLocaleCode($code);

            throw $e;
        }

        // save the code
        self::$code = $code;

        // get the name of the locale file
        $file = self::$directory.self::$code.'.csv';

        // the file must exist
        if (!is_file($file)) {
            $e = new LocaleException(
                'unknown language code. You can create the locale file and push it on the Html2Pdf GitHub project.'
            );
            $e->setLocaleCode($code);

            throw $e;
        }

        // load the file
        self::$list = array();
        $handle = fopen($file, 'r');
        while (!feof($handle)) {
            $line = fgetcsv($handle);
            if (!is_array($line) || count($line) !=2) {
                continue;
            }
            self::$list[trim($line[0])] = trim($line[1]);
        }
        fclose($handle);
    }

    /**
     * clean the locale
     *
     * @return void
     */
    public static function clean()
    {
        self::$code = null;
        self::$list = array();
    }

    /**
     * get a text
     *
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public static function get($key, $default = '######')
    {
        return (isset(self::$list[$key]) ? self::$list[$key] : $default);
    }
}
