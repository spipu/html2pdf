# Bookmark

[back](./README.md)

## tag bookmark

You can add automatic bookmark,  by directly inserting the `<bookmark>` tag in the HTML to convert :

```html
<bookmark title="My Title" level="0" ></bookmark>
```

### attributes

Attribute| Default | Description
---------|---------|-------------
title | | Title of the bookmark
level | 0 | Level of the bookmark, must be a positive integer. Level 0 is the main level

## Page Index

You can insert an index (summary) of all bookmarks automatically, using the following function : 

```php
$html2pdf->createIndex($titre, $sizeTitle, $sizeBookmark, $bookmarkTitle, $displayPage, $onPage, $fontName, $marginTop);
```

### parameters

Parameter| Default | Description
---------|---------|-------------
$title | Index | index title
$sizeTitle | 20 | font size of the index title, in mm
$sizeBookmark | 15 | font size of the index, in mm
$bookmarkTitle | true | add a bookmark for the index, at his beginning
$displayPage | true | display the page numbers
$onPage | null | if null : at the end of the document on a new page, else on the $onPage page
$fontName | null | font name to use. If null, use helvetica
$marginTop | null | margin top to use on the index page

**IMPORTANT**:
If you want the summary index on a specific page (using $onPage) you must have anticipated this page during the creation of HTML (see example below).
Furthermore, if the summary index takes more than one page, you must have provided the necessary number of pages...

## Example with automatic index on last page

```html
<style type="text/css">
<!--
    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}
    h1 {color: #000033}
    h2 {color: #000055}
    h3 {color: #000077}
    
    div.standard
    {
        padding-left: 5mm;
    }
-->
</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
    <page_header>
        <table class="page_header">
            <tr>
                <td style="width: 100%; text-align: left">
                    Example of using bookmarks
                </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    page [[page_cu]]/[[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>
    <bookmark title="Chapter 1" level="0" ></bookmark><h1>Chapter 1</h1>
    <div class="standard">
        Contents of Chapter 1
    </div>
</page>
<page pageset="old">
    <bookmark title="Chapter 2" level="0" ></bookmark><h1>Chapter 2</h1>
    <div class="standard">
        Intro to Chapter 2
        <bookmark title="Chapter 2.1" level="1" ></bookmark><h2>Chapter 2.1</h2>
        <div class="standard">
            Contents of Chapter 2.1
        </div>
        <bookmark title="Chapter 2.2" level="1" ></bookmark><h2>Chapter 2.2</h2>
        <div class="standard">
            Contents of Chapter 2.2
        </div>
        <bookmark title="Chapter 2.3" level="1" ></bookmark><h2>Chapter 2.3</h2>
        <div class="standard">
            Contents of Chapter 2.3
        </div>
    </div>
</page>
<page pageset="old">
    <bookmark title="Chapter 3" level="0" ></bookmark><h1>Chapter 3</h1>
    <div class="standard">
        Intro to Chapter 3
        <bookmark title="Chapter 3.1" level="1" ></bookmark><h2>Chapter 3.1</h2>
        <div class="standard">
            Contents of Chapter 3.1
        </div>
        <bookmark title="Chapter 3.2" level="1" ></bookmark><h2>Chapter 3.2</h2>
        <div class="standard">
            Intro to Chapter 3.2
            <bookmark title="Chapter 3.2.1" level="2" ></bookmark><h3>Chapter 3.2.1</h3>
            <div class="standard">
                Contents of Chapter 3.2.1
            </div>
            <bookmark title="Chapter 3.2.2" level="2" ></bookmark><h3>Chapter 3.2.2</h3>
            <div class="standard">
                Contents of Chapter 3.2.2
            </div>
        </div>
    </div>
</page>
```

```php
$html2pdf = new Spipu\Html2Pdf\Html2Pdf('P','A4','en');
$html2pdf->writeHTML($html);
$html2pdf->createIndex('Summary', 25, 12, true, true);
$html2pdf->output();
```

## Example with automatic index on specific page

```html
<style type="text/css">
<!--
    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}
    h1 {color: #000033}
    h2 {color: #000055}
    h3 {color: #000077}
    
    div.standard
    {
        padding-left: 5mm;
    }
-->
</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
    <page_header>
        <table class="page_header">
            <tr>
                <td style="width: 100%; text-align: left">
                    Example of using bookmarks
                </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    page [[page_cu]]/[[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>
    <bookmark title="Summary" level="0" ></bookmark>
</page>
<page pageset="old">
    <bookmark title="Chapter 1" level="0" ></bookmark><h1>Chapter 1</h1>
    <div class="standard">
        Contents of Chapter 1
    </div>
</page>
<page pageset="old">
    <bookmark title="Chapter 2" level="0" ></bookmark><h1>Chapter 2</h1>
    <div class="standard">
        Intro to Chapter 2
        <bookmark title="Chapter 2.1" level="1" ></bookmark><h2>Chapter 2.1</h2>
        <div class="standard">
            Contents of Chapter 2.1
        </div>
        <bookmark title="Chapter 2.2" level="1" ></bookmark><h2>Chapter 2.2</h2>
        <div class="standard">
            Contents of Chapter 2.2
        </div>
        <bookmark title="Chapter 2.3" level="1" ></bookmark><h2>Chapter 2.3</h2>
        <div class="standard">
            Contents of Chapter 2.3
        </div>
    </div>
</page>
<page pageset="old">
    <bookmark title="Chapter 3" level="0" ></bookmark><h1>Chapter 3</h1>
    <div class="standard">
        Intro to Chapter 3
        <bookmark title="Chapter 3.1" level="1" ></bookmark><h2>Chapter 3.1</h2>
        <div class="standard">
            Contents of Chapter 3.1
        </div>
        <bookmark title="Chapter 3.2" level="1" ></bookmark><h2>Chapter 3.2</h2>
        <div class="standard">
            Intro to Chapter 3.2
            <bookmark title="Chapter 3.2.1" level="2" ></bookmark><h3>Chapter 3.2.1</h3>
            <div class="standard">
                Contents of Chapter 3.2.1
            </div>
            <bookmark title="Chapter 3.2.2" level="2" ></bookmark><h3>Chapter 3.2.2</h3>
            <div class="standard">
                Contents of Chapter 3.2.2
            </div>
        </div>
    </div>
</page>
```

```php
$html2pdf = new Spipu\Html2Pdf\Html2Pdf('P','A4','en');
$html2pdf->writeHTML($html);
$html2pdf->createIndex('Summary', 25, 12, false, true, 1);
$html2pdf->output();
```

[back](./README.md)
