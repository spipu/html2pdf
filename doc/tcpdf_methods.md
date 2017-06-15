# TCPDF Methods

[back](./README.md)

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
$html2pdf->output();
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
$html2pdf->output();
```

## Document Protection

You can protect your PDF document, with the `setProtection` method:

```php
$html2pdf->pdf->SetProtection($permissions, $userPass, $ownerPass, $mode, $pubkeys);
```

The parameters are:

Parameter| Default | Description
---------|---------|-------------
$permissions | | the set of permissions (specify the ones you want to block):<ul><li>print : Print the document;</li><li>modify : Modify the contents of the document by operations other than those controlled by 'fill-forms', 'extract' and 'assemble';</li><li>copy : Copy or otherwise extract text and graphics from the document;</li><li>annot-forms : Add or modify text annotations, fill in interactive form fields, and, if 'modify' is also set, create or modify interactive form fields (including signature fields);</li><li>fill-forms : Fill in existing interactive form fields (including signature fields), even if 'annot-forms' is not specified;</li><li>extract : Extract text and graphics (in support of accessibility to users with disabilities or for other purposes);</li><li>assemble : Assemble the document (insert, rotate, or delete pages and create bookmarks or thumbnail images), even if 'modify' is not set;</li><li>print-high : Print the document to a representation from which a faithful digital copy of the PDF content could be generated. When this is not set, printing is limited to a low-level representation of the appearance, possibly of degraded quality.</li><li>owner : (inverted logic - only for public-key) when set permits change of encryption and enables all other permissions.</li></ul>
$userPass | | user password. Empty by default.
$ownerPass | null | owner password. If not specified, a random value is used.
$mode | 0 | encryption strength: 0 = RC4 40 bit; 1 = RC4 128 bit; 2 = AES 128 bit; 3 = AES 256 bit.
$pubkeys | null| array of recipients containing public-key certificates ('c') and permissions ('p'). For example: array(array('c' => 'file://../examples/data/cert/tcpdf.crt', 'p' => array('print')))

[back](./README.md)
