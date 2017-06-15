# Basic Usage

[back](./README.md)

## PHP Constructor

The main class of this library is `\Spipu\Html2Pdf\Html2Pdf`.

The PHP constructor takes the following parameters:

Variable | Default value |Description
---------|---------------|--------------
$orientation | P | The default page orientation, can be P (portrait) or L (landscape)
$format | A4 | The default page format used for pages. The list of the available value are [here](https://github.com/tecnickcom/TCPDF/blob/master/include/tcpdf_static.php#L2097). You can also give a array with 2 values the width and the height in mm.
$lang | fr | Language to use, for some minor translations. The list of the available languages are [here](https://github.com/spipu/html2pdf/tree/master/src/locale)
$unicode | true | means that the input HTML string is unicode
$encoding |UTF-8 | charset encoding of the input HTML string
$margins | array(5, 5, 5, 8) | Main margins of the page (left, top, right, bottom) in mm
$pdfa | false | If TRUE set the document to PDF/A mode

In most of the case, you will just use the 3 first parameters :

```php
$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
```

## Convert the HTML

The main method to use is `writeHTML`. 

It takes one parameter : the HTML in string format that you want to convert into PDF.

```php
$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first test');
```

You can call it more than one time, if you want to split the conversion in order to use less memory. It will continue on the same page, directly at the end of the last converted part.

```php
$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first text');
$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my second text');
```

If you want to separate on a new page, you can use the specific HTML tag `page`.

```php
$html2pdf->writeHTML('<page><h1>HelloWorld</h1>This is my first page</page>');
$html2pdf->writeHTML('<page><h1>HelloWorld</h1>This is my second page</page>');
```

You can find more information about this specific tag on the [page](page.md) documentation.

## Get the PDF

The main method to use is `output`.
 
It takes two not required parameters. You can find more information on the [output](output.md) documentation.

If you do not give any parameters, it will send the PDF file to the browser, to display it.

```php
$html2pdf->output();
```

## Full Example

Here is the full code for a helloworld example:

```php
$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first page');
$html2pdf->output();
```

[back](./README.md)
