<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Tag\Html;
use Spipu\Html2Pdf\Tag\AbstractHtmlTag;
/**
 * Tag StrikeTag
 */
class StrikeTag extends AbstractHtmlTag
{
    public function __construct()
    {
        parent::__construct('strike');
    }

    public function getName()
    {
        return 'strike';
    }

    protected function overrideStyles()
    {
        $this->parsingCss->value['font-linethrough'] = true;
        return true;
    }
}