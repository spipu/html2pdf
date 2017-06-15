# TCPDF Methods

All the TCPDF methods can be used, by using the `pdf` property:

```php
$html2pdf->pdf->...
```

## Display Mode

You can change how your PDF document will be displayed, with the `SetDisplayMode` method:

```php
$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->writeHTML($htmlContent);
$html2pdf->Output();
```

The parameters are:

Parameter| Default | Description
---------|---------|-------------
$zoom | | The zoom to use. It can be one of the following string values or a number indicating the zooming factor to use. <ul><li>fullpage: displays the entire page on screen </li><li>fullwidth: uses maximum width of window</li><li>real: uses real size (equivalent to 100% zoom)</li><li>default: uses viewer default mode</li></ul>
$layout | SinglePage | The page layout. Possible values are:<ul><li>SinglePage Display one page at a time</li><li>OneColumn Display the pages in one column</li><li>TwoColumnLeft Display the pages in two columns, with odd-numbered pages on the left</li><li>TwoColumnRight Display the pages in two columns, with odd-numbered pages on the right</li><li>TwoPageLeft (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the left</li><li>TwoPageRight (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the right</li></ul>
$mode | UseNone | A name object specifying how the document should be displayed when opened:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>FullScreen Full-screen mode, with no menu bar, window controls, or any other window visible</li><li>UseOC (PDF 1.5) Optional content group panel visible</li><li>UseAttachments (PDF 1.6) Attachments panel visible</li></ul>

## Document Information

You can change the document information, with the following methods:

```php
$html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
$html2pdf->pdf->SetAuthor('LAST-NAME Frist-Name');
$html2pdf->pdf->SetTitle('My Pdf Document');
$html2pdf->pdf->SetSubject('it will be about something important');
$html2pdf->pdf->SetKeywords('example, keywords, others');
$html2pdf->writeHTML($htmlContent);
$html2pdf->Output();
```

## Document Protection

http://wiki.spipu.net/doku.php?id=html2pdf:en:v4:protect