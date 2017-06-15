# Extensions and Tags

[back](./README.md)

## Html Tags

All the existing tags are under the namespace `\Spipu\Html2Pdf\Tag`.

A tag must implement the interface `\Spipu\Html2Pdf\Tag\TagInterface`.

A tag can extends the abstract class `\Spipu\Html2Pdf\Tag\AbstractTag` to implement some generic methods.

Here is a tag example that will do nothing:

```php
<?php
namespace Example\Html2Pdf\Tag;

use \Spipu\Html2Pdf\Tag\AbstractTag;

/**
 * Tag Example
 */
class Example extends AbstractTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'example';
    }

    /**
     * Open the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function open($properties)
    {
        // there is nothing to do here

        return true;
    }

    /**
     * Close the HTML tag
     *
     * @param array $properties properties of the HTML tag
     *
     * @return boolean
     */
    public function close($properties)
    {
        // there is nothing to do here

        return true;
    }
}
```

Then, you will be able to use the `<example>` html tag.

Look at all the existing tags in the `/src/Tag` folder to see examples.

## Extensions

In order to add your new tags to Html2Pdf, you must a extension.

An extension must implement the interface `\Spipu\Html2Pdf\Extension\ExtensionInterface`.

You must implement the `getTags` method that will return a list of tag objects.

```php
<?php
/**
 * Extension Example
 */
namespace Example\Html2Pdf\Extension;

use \Spipu\Html2Pdf\Extension\ExtensionInterface;

/**
 * Class MyExtension
 */
class MyExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    private $tagDefinitions = array();

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'example_extension';
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        if (empty($this->tagDefinitions)) {
            $this->tagDefinitions = array(
                new \Example\Html2Pdf\Tag\Example(),
                new \Example\Html2Pdf\Tag\Other(),
            );
        }

        return $this->tagDefinitions;
    }
}
```

Then, you can add your extension to Html2Pdf with the following method:

```php
$html2pdf->addExtension(new \Example\Html2Pdf\Extension\MyExtension());
```

A core extension is automatically added to Html2pdf: `\Spipu\Html2Pdf\Extension\CoreExtension`

It contains all the native tags.

## Overriding

If an extension has the same name that an already added extension, it will replace the first one.

If tag has the same name that an already added tag throw an extension, it will replace the first one.

[back](./README.md)
