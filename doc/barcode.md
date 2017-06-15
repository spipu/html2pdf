# BarCode

[back](./README.md)

## tag barcode

You can add barcode, by directly inserting the `<barcode>` tag in the HTML to convert :

```html
<barcode dimension="1D" type="EAN13" value="45" label="label" style="width:30mm; height:6mm; color: #770000; font-size: 4mm"></barcode>
```

### attributes

Attribute| Default | Description
---------|---------|-------------
dimension | 1D | create a 1D or 2D barcode
type| C39 | type of barcode to use
value| 0 | value to convert into barcode
label| label | indicates that the label must be present below the bar code (label) or not (none) (not required)
style| | sets the color for the bar, its width and height (without the label) and the size of the label if it is displayed (not required)

### 1D types

For 1D bar-codes, the possible values for `type` attribute are:

Type| Description
----|------------
C39| CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
C39+| CODE 39 with checksum
C39E| CODE 39 EXTENDED
C39E+| CODE 39 EXTENDED + CHECKSUM
C93| CODE 93 - USS-93
S25| Standard 2 of 5
S25+| Standard 2 of 5 + CHECKSUM
I25| Interleaved 2 of 5
I25+| Interleaved 2 of 5 + CHECKSUM
C128| CODE 128
C128A| CODE 128 A
C128B| CODE 128 B
C128C| CODE 128 C
EAN2| 2-Digits UPC-Based Extension
EAN5| 5-Digits UPC-Based Extension
EAN8| EAN 8
EAN13| EAN 13
UPCA| UPC-A
UPCE| UPC-E
MSI| MSI (Variation of Plessey code)
MSI+| MSI + CHECKSUM (modulo 11)
POSTNET| POSTNET
PLANET| PLANET
RMS4CC| RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)
KIX| KIX (Klant index - Customer index)
IMB| IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200
IMBPRE| IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200- pre-processed
CODABAR| CODABAR
CODE11| CODE 11
PHARMA| PHARMACODE
PHARMA2T| PHARMACODE TWO-TRACKS

### 2D types

For 2D barcodes, the possible values for `type` attribute are:

Type| Description
----|------------
DATAMATRIX| DATAMATRIX (ISO/IEC 16022)
PDF417| PDF417 (ISO/IEC 15438:2006)
QRCODE| QR-CODE
RAW| RAW MODE
RAW2| RAW MODE

## tag qrcode

You can directly add bar-codes to two-dimensional QR-Code, by inserting the tag QRcode directly in the HTML to convert:

```html
<qrcode value="Value to Coder" ec="H" style="width: 50mm; background-color: white; color: black;"></qrcode>
```

### attributes

Attribute| Default | Description
---------|---------|-------------
value| | value to convert into barcode
ec| H | level of error correction (L, M, Q, H)
style| | sets the width, color, background-color, and border of the qrcode

[back](./README.md)