# How to Install Html2Pdf

[back](./README.md)

## Composer and Packagist

You have to use Composer to install Html2Pdf.

If you do not know what is Composer:

* You can find the documentation on https://getcomposer.org/doc/
* You can find all the available packages on https://packagist.org/
* For example, you can find Html2Pdf: https://packagist.org/packages/spipu/html2pdf

## Install

You just have to launch the following command on the root folder of your project:

```bash
composer require spipu/html2pdf
```

If you do not want to use composer, you will need to:

* manually clone the Html2pdf repository
* manually clone all the repositories of the used dependencies
* manage manually the PS4 autoload

But it is not the recommended way to install Html2Pdf. No help will be provided in this case.

### First Test

Here is a HelloWorld example, that you can put on the root folder of your project.

```php
require __DIR__.'/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf();
$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first test');
$html2pdf->output();
```

Html2Pdf use the PSR-4 autoloader of Composer. You have just to require it. Never require manually the classes, it will not work at all. You must use the Composer functionalities.

Then, you have just to use the main class `Spipu\Html2Pdf\Html2Pdf`, with the 2 main methods `writeHTML` and `output`.

[back](./README.md)
