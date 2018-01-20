# Output

[back](./README.md)

The main method to use is `output`.
 
It takes two not required parameters.

## Parameters

Parameter| Default | Description
---------|---------|-------------
$name | document.pdf | The name of the file when saved. Note that special characters are removed and blanks characters are replaced with the underscore character.
$dest | I | Destination where to send the document. It can take one of the following values:<ul><li>I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.</li><li>D: send to the browser and force a file download with the name given by name.</li><li>F: save to a local server file with the name given by name.</li><li>S: return the document as a string (name is ignored).</li><li>FI: equivalent to F + I option</li><li>FD: equivalent to F + D option</li><li>E: return the document as base64 mime multi-part email attachment (RFC 2045)</li></ul>

## Examples

### Send PDF to browser without specifying a name

```php
$html2pdf->output(); 
```

### Send the PDF document in browser with a specific name

```php
$html2pdf->output('my_doc.pdf'); 
```

### Forcing the download of PDF via web browser, with a specific name

```php
$html2pdf->output('my_doc.pdf', 'D'); 
```

### Write the contents of a PDF file on the server

```php
$html2pdf->output('/absolute/path/file_xxxx.pdf', 'F');
```

### Retrieve the contents of the PDF and then do whatever you want

```php
$pdfContent = $html2pdf->output('my_doc.pdf', 'S');
```

Then, you can send it by email, using a Bin Attachment document.

[back](./README.md)
