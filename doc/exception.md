# Exception

[back](./README.md)

## Exceptions

All the specific Html2Pdf exceptions are under the namespace `\Spipu\Html2Pdf\Exception`;

Exception|Error Code|Additional Info|Description
---------|----------|---------------|-----------
Html2PdfException | 0 | | Occurs for every generic error during the process
HtmlParsingException | 1 | <ul><li>getInvalidTag</li><li>getHtmlLine</li></ul> | Occurs if the html is no valid
ImageException | 2 | <ul><li>getImage</li></ul> | Occurs if the asked image does not exist
LongSentenceException | 3 | <ul><li>getSentence</li><li>getWidthBox</li><li>getLength</li></ul> | Occurs is a sentence is too long and does not fit in the current box
TableException | 4 | | Occurs if the content of a TD does not fit on only one page

## Exception Format

An exception formatter can be used to display the exceptions: `\Spipu\Html2Pdf\Exception\ExceptionFormatter`.

It takes the current exception as a parameter of the constructor.

It provides 2 methods

  * getMessage()
  * getHtmlMessage();

Usage example:

```php
try {
    use Spipu\Html2Pdf\Html2Pdf;
    use Spipu\Html2Pdf\Exception\Html2PdfException;
    use Spipu\Html2Pdf\Exception\ExceptionFormatter;

    $html2pdf = new Html2Pdf('P', 'A4', 'fr');
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($htmlContent);
    $html2pdf->output();
} catch (Html2PdfException $e) {
    html2pdf->clean();

    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
```

[back](./README.md)
