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
 * Tag Span
 */
class Span extends AbstractHtmlTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'span';
    }

    /**
     * @inheritdoc
     */
    public function close($properties)
    {
        $this->parsingCss->restorePosition();

        return parent::close($properties);
    }
}
