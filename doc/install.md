# How to Install Html2Pdf

[back](./README.md)

## Composer and Packagist

You must use Composer to install Html2Pdf.

If you do not know what is Composer, you are a few years late...

It is used by all the modern PHP applications (Magento2, Drupal, EasyPlatform, Symfony, ...).

You can read all the pages on https://getcomposer.org/doc/

You can find all the available packages on https://packagist.org/

For example, you can find Html2Pdf: https://packagist.org/packages/spipu/html2pdf

You have to commit the `composer.json` and `composer.lock` files, but **never** commit the `vendor` folder.

If you do not understand why, it is because you have not read the Composer documentation...

## Install

You have just to launch the following command on the root folder of your project:

```bash
composer require spipu/html2pdf
```

### First Test

Here is a HelloWorld example, that you can put on the root folder of your project.

```php
require __DIR__.'/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf();
$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first test');
$html2pdf->output();
```

Html2Pdf use the PSR-4 autoloader of Composer. You have just to require it. Never require manually the classes, it will not work at all. You must use the Composer functionnalities.

Then, you have just to use the main class `Spipu\Html2Pdf\Html2Pdf`, with the 2 main methods `writeHTML` and `output`.

### And on production ?

You have **not** to install composer on your production server. 

You have to install composer **only** on your dev environement. Composer is a dev tool.
 
To deliver you app on a server, you have to (on you dev environement) :

  * Git clone the tag/branch that you want to deliver
  * Launch the command `composer install --no-dev`
  * Remove the useless files (like the `.git` folder)
  * Zip all

That's all, you have a beautifull package that can be deliver on a server !

[back](./README.md)
