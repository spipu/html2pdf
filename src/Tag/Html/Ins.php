<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag\Html;

/**
 * Tag Ins
 */
class Ins extends U
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ins';
    }
}
