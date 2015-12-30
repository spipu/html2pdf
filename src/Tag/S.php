<?php
/**
 * Html2Pdf Library - Tag class
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

/**
 * Tag S
 */
class S extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 's';
    }

    /**
     * override some styles
     *
     * @return Span
     */
    protected function overrideStyles()
    {
        $this->parsingCss->value['font-linethrough'] = true;

        return $this;
    }
}
