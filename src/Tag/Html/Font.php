<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag\Html;

/**
 * Tag Font
 */
class Font extends Span
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'font';
    }
}
