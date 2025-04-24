<?php
namespace Spipu\Html2Pdf\Tag\Html;

use Spipu\Html2Pdf\Tag\AbstractTag;

/**
 * Button Tag class
 */
class Button extends AbstractTag
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'button';
    }

    /**
     * @inheritdoc
     */
    public function open($properties)
    {
        $styles = $this->getStyleFromProperties($properties);

        // Set default button styling if not specified
        if (!isset($styles['background'])) {
            $styles['background'] = '#f0f0f0';
        }
        if (!isset($styles['border'])) {
            $styles['border'] = '1px solid #ccc';
        }
        if (!isset($styles['padding'])) {
            $styles['padding'] = '5px 10px';
        }

        // Add custom class if provided
        if (isset($properties['class'])) {
            $this->parsingCss->analyse('button.' . $properties['class']);
        }

        // Handle button type
        $type = isset($properties['type']) ? $properties['type'] : 'button';
        
        // Create button container with proper styling
        $this->pdf->setStyle($styles);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function close($properties)
    {
        // Reset styles after button closure
        $this->pdf->resetStyle();
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getStyleFromProperties($properties)
    {
        $styles = [];

        // Handle standard CSS properties
        $cssProperties = ['width', 'height', 'background', 'color', 'border', 'padding', 'margin', 'font-size'];
        foreach ($cssProperties as $prop) {
            if (isset($properties[$prop])) {
                $styles[$prop] = $properties[$prop];
            }
        }

        return $styles;
    }
}
