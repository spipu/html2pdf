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

use Spipu\Html2Pdf\Tag\AbstractHtmlTag;

/**
 * Tag U
 */
class U extends AbstractHtmlTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'u';
    }

    /**
     * @inheritdoc
     */
    protected function overrideStyles()
    {
        $this->parsingCss->value['font-underline'] = true;

        return $this;
    }
}
