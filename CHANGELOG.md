# Change Log

All notable changes to this project will be documented in this file.

## [5.2.8](https://github.com/spipu/html2pdf/compare/v5.2.7...v5.2.8) - 2023-07-18

  * fix XSS vulnerabilities in examples `example9.php` and `forms.php` - thanks to Michał Majchrowicz, Livio Victoriano and Zbigniew Piotrak  from [AFINE  Team](https://www.afine.pl/)

## [5.2.7](https://github.com/spipu/html2pdf/compare/v5.2.6...v5.2.7) - 2023-02-02

  * fix phpunit compatibility

## [5.2.6](https://github.com/spipu/html2pdf/compare/v5.2.5...v5.2.6) - 2023-01-28

  * add support of PHP 8.1 and PHP 8.2
  * add phpunit 9 compatibility - thanks to @jausions
  * remove useless files

## [5.2.5](https://github.com/spipu/html2pdf/compare/v5.2.4...v5.2.5) - 2022-04-04

  * fix security on scheme of css and image paths for windows paths

## [5.2.4](https://github.com/spipu/html2pdf/compare/v5.2.3...v5.2.4) - 2021-12-16

  * revert fix multibyte aware substr when setting newline position - it causes pbs on some specific cases
  * security #CVE-2021-45394 - add security on scheme of css and image paths - thanks to Clément Amic and Antoine Gicquel from [Synacktiv](https://www.synacktiv.com/)

## [5.2.3](https://github.com/spipu/html2pdf/compare/v5.2.2...v5.2.3) - 2021-10-19

  * add support for BASE64 encoded images also for backimg tag in page - thanks to @berengan
  * fix issue on write2DBarcode parameters - thanks to @Sarigue
  * fix svg path parser - thanks to @CWBudde - issue #618
  * fix html attribute parser - thanks to @Tofandel
  * fix multibyte aware substr when setting newline position - thanks to @AndyTWF
  * add php8 compatibility and fix tcpdf compatibilty - thanks to @humancopy

## [5.2.2](https://github.com/spipu/html2pdf/compare/v5.2.1...v5.2.2) - 2020-03-22

  * allow usage of [[page_cu]] in css class names - thanks to @marbetschar - see example 15
  * add support for BASE64 encoded images - thanks to @darius-heavy
  * add Chinese local file - thanks to @Jaggle
  * add powershell test script
  * bump supported version from 5.4-7.2 to 5.6-7.4 - thanks to @coffeemedia
  * fix issue on \_drawRectangle where array offset was being accessed on value of type null - thanks to @coffeemedia
  * fix issue on lower-roman style - thanks to @jigneshsolanki
  * fix issue on composer autoload warning - thanks to @adrienfr

## [5.2.1](https://github.com/spipu/html2pdf/compare/v5.2.0...v5.2.1) - 2018-10-26

  * add `cert` tag - thanks to @LittleBigFox
  * add `balloon` example - issue #385
  * change the name of all the examples from `exemple` to `example`
  * fix tag parser - css style corruption
  * fix issue on relative margin calculation
  * fix issue on border radius calculation
  * fix issue on page background image width - issue #394
  * fix issue on qrcode value with multi lines - issue #390
  * fix issue on colspan usage when all the columns don't really exist - issue #360
  * fix documentation - thanks to @noxlux and @tpohchai

## [5.2.0](https://github.com/spipu/html2pdf/compare/v5.1.0...v5.2.0) - 2018-07-31

  * change the name of `CoreExtension` to `Core\HtmlExtension`
  * change the namespace of all the html tags from `Tag\` to `Tag\Html`
  * change all the svg tag from internal methods to new external tags
  * add the extension `Core\SvgExtension`
  * add locale fi - thanks to @RWAP
  * add `$marginTop` parameter on the `createIndex` method - issue #333
  * add `xx-large` to `xx-small` font-size management - issue #320
  * add `colgoup` html tag - issue #306
  * fix locale pt - thanks to @marcoshenzel
  * fix issue on testing the filename when asking for string output
  * fix issue - clean locale before PDF generation - thanks to @quimcalpe
  * fix issue on radio/checkbow width - thanks to @Ohda
  * fix issue on line number when using style tag - issue #338
  * fix issue on svg draw path - relative move was not implemented
  * fix issue on svg draw path when Z directive is not separate from the next directive - issue #308
  * fix issue on justify text with an inline html tag at the end of a line - issue #258
  * fix better doc

## [5.1.0](https://github.com/spipu/html2pdf/compare/v5.0.1...v5.1.0) - 2018-01-23

  * add support of css `page-break-before:always` and `page-break-after:always` on `DIV` tag, based on PR #190 and PR #204
  * add no-html mode for debug output
  * add php 7.2 compatibility
  * add some unit tests, better coverage
  * fix lots of bad method names and minor improvements, from PR #147
  * fix bad cleaning after output or exception
  * fix bad format detecting on `page` tag - issue #260
  * fix changelog file
  
## [5.0.1](https://github.com/spipu/html2pdf/compare/v5.0.0...v5.0.1) - 2017-06-15

  * fix issue #200 pb with absolute path when saving the pdf file on server 

## [5.0.0](https://github.com/spipu/html2pdf/compare/v4.6.1...v5.0.0) - 2017-06-15

**BREAK COMPATIBILITY**

This new version is not compatible with the 4.x.x version.

Lots of classes / methods have been renamed, moved, deleted, exploded.

  * change licence to OSL-3.0
  * change PHP minimum compatibility to 5.4
  * change Using Namespaces
  * change all the classes have moved
  * change all the classes have been renamed
  * change new tag structure : one class per tag
  * change output method does not allow bool value on `$dest` parameter anymore
  * change output method has been renamed from `Output` to `output`
  * change values for the footer`attribute` of the `page` tag
  * add PHP 7.0 and 7.1 compatibility
  * add improve string handling for UTF8
  * add better exception management
  * add unit testing
  * add support 'start' attribute for ordered list
  * add russian language https://github.com/spipu/html2pdf/pull/131
  * add Dimension-Parameter on barcode (for PDF417, Datamatrix = 2D) https://github.com/spipu/html2pdf/pull/127
  * add `pdfa` parameter on Html2Pdf constructor https://github.com/spipu/html2pdf/pull/122
  * add new documentation folder `./doc/`
  * add new attribute to page tag `hidefooter` which accepts a list of pages that gonna skip footer https://github.com/spipu/html2pdf/issues/162
  * add protection on the fallback image if it does not exist
  * add protection on thead and tfoot tags: they must contain at least one tr tag
  * add norwegian locale 
  * fix a infinite loop case when reading a svg path
  * fix issue from https://github.com/spipu/html2pdf/pull/177
  * fix issue from https://github.com/spipu/html2pdf/pull/163
  * fix bug on div position https://github.com/spipu/html2pdf/issues/73
  * fix margin-bottom on table https://github.com/spipu/html2pdf/issues/108
  * fix position of fallback image
  * fix li bullet points altered by uppercase styles
  * fix save PDF file on server https://github.com/spipu/html2pdf/issues/164
  * remove old barcode type compatibility

## [4.6.1](https://github.com/spipu/html2pdf/compare/v4.6.0...v4.6.1) - 2016-04-05

  * fix css font-family lowercase check on inherit value

## [4.6.0](https://github.com/spipu/html2pdf/compare/v4.5.1...v4.6.0) - 2016-03-30

  * add Support 'start' attribute for ordered list
  * add Enable RTL languages support
  * add fallback support for images

## [4.5.1](https://github.com/spipu/html2pdf/compare/v4.5.0...v4.5.1) - 2016-03-03

  * Support the "inherit" value for font-family
  * Allow the HTML font tag to define a color attribute
  * Support for max-width and max-height attributes for images
  * Fix "border: 0" being displayed

## [4.5.0](https://github.com/spipu/html2pdf/compare/v4.4.0...v4.5.0) - 2015-12-18

  * add tag 'end_last_page' with property 'end_height'. Update example 5 to use it
  * better composer.json file
  * update TCPDF from 5.0.002 to v6.2.12 => important changes. See the TCPDF changelogs

## [4.4.0](https://github.com/spipu/html2pdf/compare/v4.03...v4.4.0) - 2015-12-11

  * includes a new attribute to page tag 'hideheader' which accepts a list of pages that gonna skip header.
  * some doc fixes, rephrasing and removing french words
  * add composer management
  * Update autoload type
  * README more readable
  * add automatic generation of pdf test files

    * script ./test/generate.sh
    * You must have the html2pdf folder in http:/localhost/html2pdf/

  * fix: Set default font from PDF_FONT_NAME_MAIN constant from TCPDF, if available
  * fix: Make space-collapsing regexp Unicode-aware
  * fix: some pbs on examples to generate them automatically

## 4.03 - 2011-05-27

  * correction de l'exemple "form.php" : vulnérabilité cross-site scripting corrigée
  * correction sur la gestion des retours à la ligne automatique
  * correction sur le calcul de la hauteur des balises H1->H6
  * amélioration de la gestion des exceptions

## 4.02 - 2011-04-29

  * ATTENTION : beaucoup de changements dans la structure du projet. version 3.xx abandonnée
  * uniformisation des fichiers du projet (standard Zend)
  * conversion des fichiers de langue en CSV, déplacement dans le répertoire "locale". création d'une classe spécifique à la gestion des locales
  * amélioration de la gestion de certaines erreurs
  * modification du nom de toutes les sous classes
  * déplacement de toutes les sous classes
  * modification du nom de toutes les méthodes protected
  * correction sur la gestion des tables
  * correction sur la lecture des path des SVG
  * premiere version de text-align:justify
  * correction sur la gestion de la balise BLOCKQUOTE
  * correction sur la gestion de la balise P
  * gestion des styles CSS pour les balises TEXTAREA, SELECT, INPUT
  * ajout de la propriété pagegroup="new" sur la balise PAGE
  * correction pour la balise INPUT de type radio : checked au lieu de selected

## 3.30 / 4.01 - 2010-05-07

  * correction sur la gestion des textes
  * correction sur le parseur HTML
  * correction sur la gestion de border-collapse
  * correction sur la gestion des TDs, H1->H6
  * ajout des balises fieldset et legend (cf exemple 4)
  * ajout de la langue CS
  * nombreuses améliorations
  * v4.01 uniquement : Utilisation de TCPDF 5.0.002
  * v4.01 uniquement : Utilisation des QR-code de TCPDF, il n'y a plus besoin d'une librairie externe
  * v4.01 uniquement : Utilisation des exceptions PHP pour les erreurs. Tous les exemples ont été mis à jour en consequence
  * (merci à Pavel Kochman pour ses sugestions et ses ajouts)

## 4.00 - 2010-03-17

  * modification des barcodes. ATTENTION : bar_w et bar_h n'existent plus !
  * correction sur la gestion de page_footer
  * correction sur la gestion des html entities
  * correction sur le positionnement des textes
  * correction sur le positionnement des tableaux
  * nombreuses corrections sur les positionnements, les couleurs, ...
  * amélioration de la partie SVG (balise G, ...)
  * amélioration sur createIndex
  * harmonisation des noms des méthodes
  * correction sur la gestion des textes
  * v4.00 uniquement : Html2Pdf est maintenant écrit en PHP5 et basé sur TCPDF (=> unicode, utf8, ...)
  * v4.00 uniquement : utilisation de TCPDF pour les formulaires et les barcodes
  * v4.00 uniquement : amélioration de la partie SVG (alpha)

## 3.28 - 2010-01-18

  * ajout de la gestion de la balise label
  * correction pour compatibilité PHP4

## 3.27 - 2010-01-11

  * correction sur page_header et page_footer
  * ajout de la possibilité de pouvoir mettre l'index automatique dans la page que l'on veut
  * correction sur la gestion du canal alpha pour les PNGs
  * correction sur la gestion des border-radius (cf exemple radius) conforme au CSS3
  * correction sur la gestion du background-color
  * correction sur la gestion de thead, tfoot, et tbody
  * ajout du dessin verctoriel (cf exemples draw, tigre, sapin)
  * ajout de la propriété label="none/label" pour la balise barcode
  * nombreux petits correctifs

## 3.26 - 2009-11-16

  * correction pour support des images générés en CGI
  * ajout de la gestion du canal alpha pour les PNGs (nécessite GD2)
  * ajout de la méthode setDefaultFont permettant de spécifier une fonte par défaut
  * ajout de la propriété format pour la balise page (cf exemple 4)
  * amélioration de la gestion des couleurs css RGB (cf exemple 2)
  * ajout de la gestion des couleurs css CMYK (cf exemple 2)
  * ajout de la propriété css overflow:hidden pour la balise div (cf exemple 2)
  * correction sur page_header et page_footer
  * ajout de la possibilité de pouvoir directement convertir le résultat d'une vraie page HTML
  * nombreux petits correctifs sur les styles

## 3.25 - 2009-10-07

  * correctif sur le calcul des tableaux dans le page_footer
  * correctif sur l'interprétation des espaces entre certaines balises
  * correction sur la gestion des balises H1, H2, H3, H4, H5, H6
  * correction sur la gestion de la balise table
  * support des balises xhtml du type span
  * ajout des balises COL (cf exemple 5), DEL, INS, et QRCODE (cf exemple 13)
  * ajout de la propriété css text-transform
  * ajout de la propriété css rotate (uniquement sur les DIV, cf exemple 8)
  * ne plus rendre obligatoire l'existence d'une image (nouvelle méthode setTestIsImage)
  * ajout d'un mode DEBUG - les anciennes fonction d'analyse des ressources ont été supprimées
  * ajout de la méthode setEncoding
  * ajout de la langue danoise DA (merci à Daniel K.)

## 3.24 - 2009-08-05

  * correction sur le calcul de la largeur des divs
  * modification pour compatibilité avec la localisation PHP
  * modification pour compatibilité avec PHP 5.3.0

## 3.23 - 2009-07-30

  * correction sur le calcul des DIVs
  * correction sur l'interpretation de certains styles CSS
  * correction de la fonction de creation d'index automatique CreateIndex
  * ATTENTION : la methode d'appel de CreateIndex a changé. Regardez l'exemple About !!!!

## 3.22a (2009-06-16

  * redistribution de Html2Pdf sous la licence LGPL !!! (au lieu de GPL)

## 3.22 - 2009-06-08

  * correction sur le background-color
  * refonte totale de la gestion de text-align. les valeurs center et right marchent maintenant meme en cas de contenu riche

## 3.21 - 2009-05-05

  * ajout de la propriété css FLOAT pour la balise IMG
  * correction sur la gestion des TFOOT
  * correction sur le positionnement des images

## 3.20 - 2009-04-06

  * ajout de la gestion des margins pour la balise DIV
  * ajout de la gestion de la propriete css LINE-HEIGHT
  * correction sur l'interpretation de la valeur de certains styles CSS (background-image, background-position, ...)
  * correction sur la reconnaissance des balises thead et tfoot
  * correction sur la balise select
  * correction sur les fichiers de langue (merci à Sinan)

## 3.19 - 2009-03-11

  * optimisation du parseur HTML - merci à Jezelinside
  * ajout de la balise TFOOT
  * amélioration de la gestion des tableaux : les contenus des balises THEAD et TFOOT sont maintenant répétés sur chaque page.
  * ajout de la balise spécifique BOOKMARK afin de créer des "marques-page"
  * possibilité de rajouter un index automatique en fin de fichier
  * ajout de la langue turque TR (merci à Hidayet)
  * amélioration de la méthode Output. Elle est maintenant également utilisable comme celle de FPDF

## 3.18 - 2009-02-22

  * correction sur les sauts de page automatique pour les balises TABLE, UL, OL
  * correction sur l'interpretation des styles pour la balise HR
  * correction sur l'interpretation du style border-collapse pour la balise TABLE
  * prise en compte de margin:auto pour les tables et les divs
  * les commentaires dans les CSS sont acceptés

## 3.17 - 2008-12-30

  * ajout de la gestion des balises INPUT (text, radio, checkbox, button, hidden, ...), SELECT, OPTION, TEXTAREA (cf exemple 14)
  * ajout de la possibilité de mettre des scripts dans le pdf (cf exemples JS)
  * correction sur le saut de page automatique pour les images
  * correction sur les sauts de lignes automatiques pour certaines balises (UL, P, ...)
  * ajout de la langue NL (merci à Roland)

## 3.16 - 2008-12-09

  * ajout de la gestion de list-style: none (cf exemple 13)
  * correction dans la gestion des fontes ajoutées à fpdf (via la méthode AddFont)
  * nombreuses corrections sur le calcul des largeurs des éléments table, div, hr, td, th
  * ajout de l'exemple about.php
  * (pour info, les PDF générés à partir des exemples sont maintenant dans le répertoire /exemples/pdf/, et sont supprimables)

## 3.15 - 2008-12-01

  * correction sur l'identification des styles en cas de valeurs multiples dans la propriete class
  * prise en compte de border-radius pour la limite des backgrounds (color et image)
  * ajout des proprietes CSS border-top-*, border-right-*, border-bottom-*, border-left-*
  * ajout de la propriété CSS list-style-image (cf exemple 12)
  * pour la balise table, ajout de l'interprétation de align="center" et align="right" (cf exemple 1)
  * correction dans le positionnement des images
  * correction de quelques bugs
  * ajout d'une fonction d'analyse des ressources getTimerDebug (cf début du fichier html2pdf.class.php)

## 3.14 - 2008-11-17

  * ajout d'une langue (pt : Brazilian Portuguese language) et amelioration de la methode vueHTML (merci à Rodrigo)
  * correction du positionnement du contenu des DIVs. gestion des proprietes valign et align
  * ajout de la propriete CSS border-collapse (cf exemple 0)
  * ajout de la propriete CSS border-radius (cf exemple 1)
  * correction de quelques bugs

## 3.13 - 2008-09-24

  * reecriture de la balise hr, avec prise en compte des styles (cf exemple 0)
  * ajout de la propriete backcolor pour la balise page (cf exemple 9)
  * ajout des proprietes backleft et backright pour la balise page afin de pouvoir changer les marges des pages (cf exemple 8)
  * nombreuses corrections sur les balises et les styles

## 3.12 - 2008-09-16

  * ajout des balises ol, ul, li (cf exemple 12)
  * correction sur le calcul de la taille des td en cas de colspan et rowspan
  * ajout de la méthode setTestTdInOnePage afin de pouvoir desactiver le test sur la taille des TD (cf exemple 11)
  * correction de quelques bugs

## 3.11 - 2008-08-29

  * ajout des balises div, p, pre, s
  * gestion des styles CSS position (relative, absolute), left, top, right, bottom (cf exemple 10)
  * meilleur gestion des border : border-style, border-color, border-width (cf exemple 10)
  * possibilité d'indiquer les marges par défault, via le constructeur (cf exemple 2)

## 3.10a - 2008-08-26

  * correction pour compatibilité php4 / php5

## 3.10 - 2008-08-25

  * ajout des liens internes (cf exemple 7)
  * gestion complete des background : image, repeat, position, color (cf exemple 1)
  * gestion de underline, overline, linethrough (cf exemple 2)
  * correction de quelques bugs

## 3.09

  * mise à jour vers fpdf version 1.6, ajout de barcode, correction de l'affichage de certains caractères spéciaux
  * correction du calcul de la hauteur de ligne de la balise br
  * detection en cas de contenu trop grand dans un TD
  * amélioration de la balise page (ajout de l'attribue pageset, avec les valeurs new et old)
  * ajout de FPDF_PROTECTION, accesible via $pdf->pdf->SetProtection(...)

## 3.08

  * version opérationnelle de page_header
  * ajout de page_footer
  * correction des borders des tableaux

## 3.07

  * correction de l'interpretation de cellspacing,
  * amélioration de la balise page_header

## 3.06

  * première gestion de la balise page_header
  * correction des dimensions des tableaux

## 3.05

  * ajout de la propriété vertical-align
  * ajout de la gestion des fichiers de langue

## 3.04

  * correction du saut de page automatique pour les tableaux
  * Ajout de propriétés à la balise PAGE

## 3.03

  * correction de bugs au niveau de la gestion des images PHP par FPDF
  * meilleure gestion des erreurs

## 3.02

  * ajout de la gestion des noms des couleurs
  * correction de la gestion des images générées par php
  * correction de quelques bugs

## 3.01

  * correction de quelques bugs
  * ajout d'une protection pour les balises non existantes

## 3.00

  * refonte totale du calcul des tableaux
  * Prise en compte des colspan et rowspan
  * 
## 2.85

  * ajout de la propriété cellspacing
  * nouvelle gestion des padding des tableaux

## 2.80

  * ajout des types de border dotted et dasheds

## 2.75

  * ajout des top, left, right, bottom pour padding et border

## 2.70

  * correction de la balise HR, ajout de la propriété padding pour les table, th, td
  * correction des dimensions, les unités px, mm, in, pt sont enfin réellement reproduites, correction de font-size, border, ...
  * ajout d'une propriété à la balise page : footer
  * correction dans l'affichage et le calcul des tables

## 2.55

  * vérification de la validité du code (ouverture / fermeture)
  * ajout des unités mm, in, pt

## 2.50

  * correction de nobreak
  * correction des marges
  * ajout de nombreuses balises

## 2.40

  * refonte totale de l'identification des styles CSS (Les héritages marchent)

## 2.39

  * corrections diverses
  * ajout de certaines propriétés (bgcolor, ...)

## 2.38

  * meilleur identification des propriétés border et color

## 2.37

  * nombreuses corrections :
  
    * balise A
    * couleur de fond
    * retour à la ligne
    * gestion des images dans un texte

## 2.36

  * ajout de la balises STRONG
  * ajout de la balise EM

## 2.35

  * amélioration de la gestion des feuilles de style

## 2.31

  * correction de quelques bugs

## 2.30

  * première version opérationnel des feuilles de style

## 2.25

  * ajout de la balise LINK pour le type text/css

## 2.20

  * premier jet de la gestion des feuilles de style, ajout de la balise STYLE

## 2.15

  * n'interpréte plus l'HTML en commentaire

## 2.10

  * ajout des balises H1 -> H6

## 2.01

  * correction de quelques bugs

## 2.00

  * première version diffusée
