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
 * Tag Small
 */
class Small extends AbstractHtmlTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'small';
    }

    /**
     * @inheritdoc
     */
    protected function overrideStyles()
    {
        $this->parsingCss->value['mini-decal']+= $this->parsingCss->value['mini-size']*0.05;
        $this->parsingCss->value['mini-size'] *= 0.82;

        return $this;
    }
}
