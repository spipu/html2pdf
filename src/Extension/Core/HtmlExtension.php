<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Extension\Core;

use Spipu\Html2Pdf\Extension\AbstractExtension;
use Spipu\Html2Pdf\Tag\Html;

/**
 * Class HtmlExtension
 */
class HtmlExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_html';
    }

    /**
     * @inheritdoc
     */
    protected function initTags()
    {
        return array(
            new Html\Address(),
            new Html\B(),
            new Html\Big(),
            new Html\Bookmark(),
            new Html\Cite(),
            new Html\Del(),
            new Html\Em(),
            new Html\Font(),
            new Html\I(),
            new Html\Ins(),
            new Html\Label(),
            new Html\S(),
            new Html\Samp(),
            new Html\Small(),
            new Html\Span(),
            new Html\Strong(),
            new Html\Sub(),
            new Html\Sup(),
            new Html\U(),
        );
    }
}
