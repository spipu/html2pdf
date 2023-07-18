<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Extension;

use Spipu\Html2Pdf\Tag\TagInterface;

/**
 * Class AbstractExtension
 */
abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    protected $tagDefinitions = array();

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        if (empty($this->tagDefinitions)) {
            $this->tagDefinitions = $this->initTags();
        }

        return $this->tagDefinitions;
    }

    /**
     * Init the tags
     *
     * @return TagInterface[]
     */
    abstract protected function initTags();
}
