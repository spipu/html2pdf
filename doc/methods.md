# Useful Methods

[back](./README.md)

## Image Must Exist

By default, if you try to use an image that Html2Pdf can not read, it will throw an `ImageException`.

You can disable this test with the following method:

```php
$html2pdf->setTestIsImage(false);
```

If you disable the test, and if an image does not exist, it will display a 16x16 grey square instead.

## FallBack Image

If you disable the "Image Must Exist" test, you can specify a fallback image with the following method: 

```php
$html2pdf->setFallbackImage($imageFilename);
```

## Can not split TD content

By default, the content of a TD should not exceed one page.

You can disable this protection with the following method:

```php
$html2pdf->setTestTdInOnePage(false);
```

**WARNING**:
If you disable this test, you may have big layouts problems on tables in multiple columns.

Rather than disabling this test it is better to break the TD content in smaller ones.

**We do not support the consequencies of disable this test.**

## Debug Mode

You can enable a debug mode by using the following method:

```php
$html2pdf->setModeDebug();
```

You can specify your own debugger. It must implement `\Spipu\Html2Pdf\Debug\DebugInterface`.

```php
$debug = new \Spipu\Html2Pdf\Debug\Debug();
$html2pdf->setModeDebug($debug);
```
## Version

You can get the current Html2Pdf version with the following methods:

```php
$html2pdf->getVersion();
$html2pdf->getVersionAsArray();
```

[back](./README.md)
