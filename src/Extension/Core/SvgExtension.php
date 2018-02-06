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

/**
 * Class SvgExtension
 */
class SvgExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_svg';
    }

    /**
     * @inheritdoc
     */
    protected function initTags()
    {
        return array(
        );
    }
}
