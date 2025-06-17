<?php
    $name = preg_replace('/[^a-zA-Z0-9]/isU', '', (string) $name);
    $name = substr($name, 0, 26);
?>
<page>
    <br>
    Ceci est un exemple de génération de PDF :)<br>
    <br>
    <img src="http://html2pdf-dev.lxd/res/example09.png.php?px=5&amp;py=20" alt="image_php" ><br>
    <br>
    Bonjour <b><?php echo $name; ?></b>, ton nom peut s'écrire : <br>
    <barcode type="C39" value="<?php echo strtoupper($name); ?>" style="color: #770000" ></barcode><hr>
    <br>
</page>
