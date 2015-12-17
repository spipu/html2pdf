<style type="text/css">
<!--
    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}
-->
</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" pagegroup="new">
    <page_header>
        <table class="page_header">
            <tr>
                <td style="width: 100%; text-align: left">
                    Exemple d'utilisation des groupes de pages
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
    Ceci est la page 1 du groupe 1
</page>
<page pageset="old">
    Ceci est la page 2 du groupe 1
</page>
<page pageset="old">
    Ceci est la page 3 du groupe 1
</page>
<?php
for ($k=2; $k<5; $k++) :
?>
<page pageset="old" pagegroup="new">
    Ceci est la page 1 du groupe <?php echo $k; ?>
</page>
<page pageset="old">
    Ceci est la page 2 du groupe <?php echo $k; ?>
</page>
<?php
endfor;
