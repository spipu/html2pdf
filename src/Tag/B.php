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
namespace Spipu\Html2Pdf\Tag;

/**
 * Tag B
 */
class B extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'b';
    }

    /**
     * override some styles
     *
     * @return Span
     */
    protected function overrideStyles()
    {
        $this->parsingCss->value['font-bold'] = true;

        return $this;
    }
}
