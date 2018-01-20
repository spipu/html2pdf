# Html2Pdf Documentation


## Documentation

  * [How to Install Html2Pdf](./install.md)
  * [Basic Usage](./basic.md)
  * [Page and Margin](./page.md)
  * [Output](./output.md)
  * [Barcode](./barcode.md)
  * [Bookmark](./bookmark.md)
  * [Font](./font.md)
  * [SVG](./svg.md)
  * [Extensions](./extension.md)
  * [Exceptions](./exception.md)
  * [Useful Methods](./methods.md)
  * [Tcpdf Methods](./tcpdf_methods.md)

## Recommandations
   
  * It is very important to provide valid HTML 4.01 to the converter, but only what is in the `<body>`.
  * Use the `<page>` tag. Does not use the `<html>` or `<body>` tag.
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
        * (QR Code is registered trademark of DENSO WAVE INCORPORATED)

     * `<bookmark></bookmark>` (CF Examples 7 & About)
    
        * Can insert bookmark in pdfs, CF Example 7 and About.
        * It is also possible to automatically create an index at the end of document (CF Example About)

     * `<end_last_page end_height="30mm"></end_last_page>` (CF Example 5)

     * css property `rotate`:
    
        * Values : 0, 90, 180, 270
        * Works only on div (cf example 8)
