# Html2Pdf

Html2Pdf is a HTML to PDF converter written in PHP.

It allows the conversion of valid HTML in PDF format, to generate documents like invoices, documentation, ...

You have to write you HTML for Html2Pdf, and not trying to convert directly an already existing html page.

Specific tags have been implemented, to adapt the html standard to a PDF usage.

## Requirements

Html2Pdf works with PHP > 5.4 and Composer.

## Change log

See the [./CHANGELOG.md](./CHANGELOG.md) file.

## Help & Support

For questions and bug reports, please use the GitHub issues page.

## Donate

You can support this project by making a [donation](http://html2pdf.fr/en/donate).

## License

This program is distributed under the OSL License. For more information see the [./LICENCE.md](./LICENCE.md) file.

Copyright 2008-2017 by Laurent Minguet

## Installation

This package **must** be installed in your project through **composer**.

```
composer require spipu/html2pdf
```

If you want to try Html2Pdf outside a main project, you can just clone the project and run composer:

```
git clone https://github.com/spipu/html2pdf.git
cd html2pdf
composer install
```

## Recommandations
   
  * Look at the [documentation](./doc/) and the [examples](./examples/) to see how it works.
  * It is very important to provide valid HTML 4.01 to the converter, but only what is in the `<body>`. Use the `<page>` tag. 
  * for borders: it is advised that they are like `solid 1mm #000000`
  * for padding, they are applicable only on tags `table`, `th`, `td`, `div`, `li`
  * A default font can be specified, if the requested font does not exist or if no font is specified: `$html2pdf->setDefaultFont('Arial');`
  * The possibility to protect your PDF is present, CF Example 7.
  * Some tests can be enabled (true) or disabled (false):
  
     * `setTestIsImage` method:      test that images must exist
     * `setTestTdInOnePage` method:  test that the contents of TDs fit on one page

  * A DEBUG mode to know the resources used is present. It is activated by adding the following command just after the contructor (see Example 0): `$htmlpdf->setModeDebug();`
  * Some specific tags have been introduced:
  
     * `<page></page>`  (CF Exemple 7)
    
        * Determines the orientation, margins left, right, top and bottom, the background image and the background color of a page, its size and position, the footer.
        * It is also possible to keep the header and footer of the previous pages, through the attribut `pageset="old"` (see Example 3 & 4)

     * `<page_header></page_header>` (CF Example 3)
     * `<page_footer></page_footer>` (CF Example 3)
     * `<nobreak></nobreak>`
    
        * Used to force the display of a section on the same page.
        * If this section does not fit into the rest of the page, a page break is done before.

     * `<barcode></barcode>`  (CF Examples 0 & 9)
    
        * Can insert barcodes in pdfs, CF Examples 0 and 9
        * the possible types of codebar are alls of TCPDF

     * `<qrcode></qrcode>` (CF Example 13)
    
        * can insert QRcode 2D barcodes
        * (QR Code is registered trademark of DENSO WAVE INCORPORATED | http://www.denso-wave.com/qrcode/)

     * `<bookmark></bookmark>` (CF Examples 7 & About)
    
        * Can insert bookmark in pdfs, CF Example 7 and About.
        * It is also possible to automatically create an index at the end of document (CF Example About)

     * css property `rotate`:
    
        * Values : 0, 90, 180, 270
        * Works only on div (cf example 8)
