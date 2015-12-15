<?php
//============================================================+
// File name   : tcpdf_config.php
// Begin       : 2004-06-11
// Last Update : 2013-02-06
//
// Description : Configuration file for TCPDF.
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2004-2013  Nicola Asuni - Tecnick.com LTD
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
//============================================================+

/**
 * Configuration file for TCPDF.
 * @author Nicola Asuni
 * @package com.tecnick.tcpdf
 * @version 4.9.005
 * @since 2004-10-27
 */

// If you define the constant K_TCPDF_EXTERNAL_CONFIG, the following settings will be ignored.

if (!defined('K_TCPDF_EXTERNAL_CONFIG')) {

    define('K_TCPDF_EXTERNAL_CONFIG', true);

    // DOCUMENT_ROOT fix for IIS Webserver
    if ((!isset($_SERVER['DOCUMENT_ROOT'])) OR (empty($_SERVER['DOCUMENT_ROOT']))) {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace(
                '\\',
                '/',
                substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF']))
            );
        } elseif (isset($_SERVER['PATH_TRANSLATED'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace(
                '\\',
                '/',
                substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF']))
            );
        } else {
            // define here your DOCUMENT_ROOT path if the previous fails (e.g. '/var/www')
            $_SERVER['DOCUMENT_ROOT'] = '/var/www';
        }
    }

    // be sure that the end slash is present
    $_SERVER['DOCUMENT_ROOT'] = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/');

	/**
	 * Installation path of tcpdf with composer.
	 */
    $vendorFolders = array(
        dirname(dirname(__FILE__)) . '/vendor/',
        dirname(dirname(__FILE__)) . '/../../',
    );
    foreach ($vendorFolders as $vendorFolder) {
        if (file_exists($vendorFolder.'autoload.php')) {
            $k_path_main = $vendorFolder . 'tecnickcom/tcpdf/';

            break;
        }
    }

    if (!isset($k_path_main)) {
        echo "
      [ERROR]
         It seems that HTML2PDF dependencies are not installed...
         you must install thems with `composer install`

";
        exit;
    }
    define ('K_PATH_MAIN', $k_path_main);

    // Automatic calculation for the following K_PATH_URL constant
	$k_path_url = $k_path_main; // default value for console mode
    if (isset($_SERVER['HTTP_HOST']) AND (!empty($_SERVER['HTTP_HOST']))) {
        if (isset($_SERVER['HTTPS']) AND (!empty($_SERVER['HTTPS'])) AND strtolower($_SERVER['HTTPS'])!='off') {
			$k_path_url = 'https://';
        } else {
			$k_path_url = 'http://';
        }
		$k_path_url .= $_SERVER['HTTP_HOST'];
		$k_path_url .= str_replace( '\\', '/', substr(K_PATH_MAIN, (strlen($_SERVER['DOCUMENT_ROOT']) - 1)));
    }

    /**
     * URL path to tcpdf installation folder (http://localhost/tcpdf/).
     * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
     */
	define ('K_PATH_URL', $k_path_url);

    /**
     * path for PDF fonts
     * use K_PATH_MAIN.'fonts/old/' for old non-UTF8 fonts
     */
    define('K_PATH_FONTS', K_PATH_MAIN.'fonts/');

    /**
     * cache directory for temporary files (url path)
     */
    define('K_PATH_URL_CACHE', K_PATH_URL.'cache/');

    /**
     *images directory
     */
    define('K_PATH_IMAGES', K_PATH_MAIN.'images/');

    /**
     * blank image
     */
    define('K_BLANK_IMAGE', K_PATH_IMAGES.'_blank.png');

    /**
     * page format
     */
    define('PDF_PAGE_FORMAT', 'A4');

    /**
     * page orientation (P=portrait, L=landscape)
     */
    define('PDF_PAGE_ORIENTATION', 'P');

    /**
     * document creator
     */
    define('PDF_CREATOR', 'HTML2PDF - TCPDF');

    /**
     * document author
     */
    define('PDF_AUTHOR', 'HTML2PDF - TCPDF');

    /**
     * header title
     */
    define('PDF_HEADER_TITLE', null);

    /**
     * header description string
     */
    define('PDF_HEADER_STRING', null);

    /**
     * image logo
     */
    define('PDF_HEADER_LOGO', null);

    /**
     * header logo image width [mm]
     */
    define('PDF_HEADER_LOGO_WIDTH', null);

    /**
     *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
     */
    define('PDF_UNIT', 'mm');

    /**
     * header margin
     */
    define('PDF_MARGIN_HEADER', 0);

    /**
     * footer margin
     */
    define('PDF_MARGIN_FOOTER', 0);

    /**
     * top margin
     */
    define('PDF_MARGIN_TOP', 0);

    /**
     * bottom margin
     */
    define('PDF_MARGIN_BOTTOM', 0);

    /**
     * left margin
     */
    define('PDF_MARGIN_LEFT', 0);

    /**
     * right margin
     */
    define('PDF_MARGIN_RIGHT', 0);

    /**
     * default main font name
     */
    define('PDF_FONT_NAME_MAIN', 'helvetica');

    /**
     * default main font size
     */
    define('PDF_FONT_SIZE_MAIN', 10);

    /**
     * default data font name
     */
    define('PDF_FONT_NAME_DATA', 'helvetica');

    /**
     * default data font size
     */
    define('PDF_FONT_SIZE_DATA', 8);

    /**
     * default monospaced font name
     */
    define('PDF_FONT_MONOSPACED', 'courier');

    /**
     * ratio used to adjust the conversion of pixels to user units
     */
    define('PDF_IMAGE_SCALE_RATIO', 1);

    /**
     * magnification factor for titles
     */
    define('HEAD_MAGNIFICATION', 1);

    /**
     * height of cell respect font height
     */
    define('K_CELL_HEIGHT_RATIO', 1);

    /**
     * title magnification respect main font size
     */
    define('K_TITLE_MAGNIFICATION', 1);

    /**
     * reduction factor for small font
     */
    define('K_SMALL_RATIO', 2/3);

    /**
     * set to true to enable the special procedure used to avoid the overlappind of symbols on Thai language
     */
    define('K_THAI_TOPCHARS', true);

    /**
     * if true allows to call TCPDF methods using HTML syntax
     * IMPORTANT: For security reason, disable this feature if you are printing user HTML content.
     */
    define('K_TCPDF_CALLS_IN_HTML', false);

	/**
	 * if true and PHP version is greater than 5, then the Error() method throw new exception instead of terminating the execution.
	 */
	define('K_TCPDF_THROW_EXCEPTION_ERROR', true);
}

//============================================================+
// END OF FILE
//============================================================+
