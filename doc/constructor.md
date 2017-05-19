# Html2Pdf constructor

The main class of this library is `Spipu\Html2Pdf\Html2Pdf`.

The PHP constructor is defined as follow:

```php
    /**
     * class constructor
     *
     * @param string  $orientation page orientation, same as TCPDF
     * @param mixed   $format      The format used for pages, same as TCPDF
     * @param string  $lang        Lang : fr, en, it...
     * @param boolean $unicode     TRUE means that the input text is unicode (default = true)
     * @param String  $encoding    charset encoding; default is UTF-8
     * @param array   $margins     Default margins (left, top, right, bottom)
     * @param boolean $pdfa        If TRUE set the document to PDF/A mode.
     * 
     * @return Html2Pdf $this
     */
    public function __construct(
        $orientation = 'P',
        $format = 'A4',
        $lang = 'fr',
        $unicode = true,
        $encoding = 'UTF-8',
        $margins = array(5, 5, 5, 8),
        $pdfa = false
    )
```

