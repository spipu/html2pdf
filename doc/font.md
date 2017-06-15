# Font

[back](./README.md)

## Creating new Font

To create new font, you should use this tutorial from TCPDF: 

https://tcpdf.org/docs/fonts/

## Adding new font

To use this new font, you must add it to Html2Pdf, by using the following method:

```php
$html2pdf->addFont($family, $style, $file);
```

The parameters are:

Parameter| Default | Description
---------|---------|-------------
$family | | Font family. The name can be chosen arbitrarily. If it is a standard family name, it will override the corresponding font.
$style  | | Font style. Possible values are (case insensitive):<ul><li>empty string: regular (default)</li><li>B: bold</li><li>I: italic</li><li>BI or IB: bold italic</li></ul>
$file   | | The font definition file. By default, the name is built from the family and style, in lower case with no spaces.

If you want to add font for normal and for bold style, you must call the `addFont` method twice, one for each font file. 

**WARNING**:
In 4em parameter constructor of Html2pdf, you must specify whether you use a Unicode font (true) or an old font (false). 

## Using new font

You have just to se the new font family name in your css.

## Setting the default font

You can set the default font to use with the following method:

```php
$html2pdf->setDefaultFont($default);
```

[back](./README.md)
