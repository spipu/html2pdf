# Page Management

## Specific Tags

To facilitate the layout, three specific tags have been added:
 
  * `<page>`
  * `<page_header>`
  * `<page_footer>`
  
They must be used as follow:

```html
 <page> 
    <page_header> 
       ...              
    </page_header> 
    <page_footer> 
       ...
    </page_footer> 
    ...
 </page> 
```

You **must not** use `<body>` and `<html>` tags.

### Page tag

You can ust the following attributes:

Attribute| Default | Description
---------|---------|-------------
pageset | new | Specify if we want to use the previous page definition (old) or a new one (new)
pagegroup | old | Specify if we are in the same page group (old) or in a new one (new)
hideheader | | comma-separate page numbers on which we want to hide the header
hidefooter | | comma-separate page numbers on which we want to hide the footer
orientation | | Portrait (P) or Lanscape (L). By default, the orientation specified in the Html2Pdf constructor
format | | Format to use The list of the available values are [here](https://github.com/tecnickcom/TCPDF/blob/master/include/tcpdf_static.php#L2097). By default, the orientation specified in the Html2Pdf constructor

@todo

backimg | url of an image
backimgx | left / center / right / value (mm, px, pt, % )
backimgy | top / middle / bottom / value (mm, px, pt, % )
backimgw | value(mm, px, pt, % )
backtop | value(mm, px, pt, % )
backbottom | value(mm, px, pt, % )
backleft | value(mm, px, pt, % )
backright | value (mm, px, pt, % )
backcolor | value of color
footer | values among (page, date, time, form), separated by;



### Page Header tag

@todo

### Page Footer tag

@todo


http://wiki.spipu.net/doku.php?id=html2pdf:en:v4:page

http://wiki.spipu.net/doku.php?id=html2pdf:en:v4:margins
