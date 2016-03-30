Html2Pdf
========

Latest stable release : v4.6.0 (2016-03-30)

How to use:
-----------

### Requirements

HTML2PDF works with PHP 5.3.2 and above.

### Installation

This package can be installed in your project through composer.

```
composer require spipu/html2pdf
```

If you wish to try html2pdf outside a main project, you can just clone the project and run composer:

```
git clone https://github.com/spipu/html2pdf.git
cd html2pdf
composer install
```

### Recommendations
   
 * Look at the examples provided to see how it works.

 * It is very important to provide valid HTML 4.01 to the converter,
   but only what is in the `<body>`. Use the `<page>` tag. 

 * for borders: it is advised that they are like `solid 1mm #000000`

 * for padding, they are applicable only on tags `table`, `th`, `td`, `div`, `li`

 * A default font can be specified, if the requested font does not exist or if no font is specified:
 
 `$html2pdf->setDefaultFont('Arial');`

 * The possibility to protect your PDF is present, CF Example 7.

 * Some tests can be enabled (true) or disabled (false):

  * `setTestIsImage` method:      test that images must exist
  
  * `setTestTdInOnePage` method:  test that the contents of TDs fit on one page


 * A DEBUG mode to know the resources used is present.
It is activated by adding the following command just after the contructor (see Example 0):
`$htmlpdf->setModeDebug();`

* Some specific tags have been introduced:

  * `<page></page>`  (CF Exemple 7 & wiki)
    * Determines the orientation, margins left, right, top and bottom, the background image
    * and the background color of a page, its size and position, the footer.
    * It is also possible to keep the header and footer of the previous pages,
    * through the attribut `pageset="old"` (see Example 3 & 4 & wiki)

  * `<page_header></page_header>` (CF Example 3 & wiki)

  * `<page_footer></page_footer>` (CF Example 3 & wiki)

  * `<nobreak></nobreak>` (CF wiki)
    * Used to force the display of a section on the same page.
    * If this section does not fit into the rest of the page, a page break is done before.

  * `<barcode></barcode>`  (CF Examples 0 & 9 & wiki)
    * Can insert barcodes in pdfs, CF Examples 0 and 9
    * the possible types of codebar are alls of TCPDF

  * `<qrcode></qrcode>` (CF Example 13 & wiki)
    * can insert QRcode 2D barcodes
    * (QR Code is registered trademark of DENSO WAVE INCORPORATED | http://www.denso-wave.com/qrcode/)

  * `<bookmark></bookmark>` (CF Examples 7 & About & wiki)
    * Can insert bookmark in pdfs, CF Example 7 and About.
    * It is also possible to automatically create an index at the end of document (CF Example About & wiki)

  * css property `rotate`:
    * Values : 0, 90, 180, 270
    * Works only on div (cf example 8)

Change log:
-----------

See on this page: http://html2pdf.fr/en/download

Help & Support:
---------------

For questions and bug reports, please use the GitHub issues page.
You may also find some help of the official forum (link below).

Information:
------------

* Programmer: Spipu
* Web Site  : http://html2pdf.fr/
* Wiki      : http://html2pdf.fr/en/wiki
* Support   : http://html2pdf.fr/en/forum

License:
--------
```
 This program is distributed under the LGPL License,
 for more information see the LICENSE file or go to
 http://www.gnu.org/licenses/lgpl.html

 Copyright 2008-2016 by Laurent Minguet
```

Thanks:
-------

 * Olivier Plathey: for have made FPDF
 * Nicola Asuni: for the changes he has agreed to make to TCPDF
 * yAronet: for hosting support forum
 * Everyone who helped me to develop this library and to bring the texts
