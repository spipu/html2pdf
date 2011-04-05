<?php
    $phrase = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed elementum, nibh eu ultricies scelerisque, est lorem dignissim elit, quis tempus tortor eros non ipsum. Mauris convallis augue ac sapien. In scelerisque dignissim elit. Donec consequat semper lectus. Sed in quam. Nunc molestie hendrerit ipsum. Curabitur elit risus, rhoncus ut, mattis a, convallis eu, neque. Morbi luctus est sit amet nunc. In nisl. Donec magna libero, aliquet eu, vestibulum ut, mollis sed, felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed elementum, nibh eu ultricies scelerisque, est lorem dignissim elit, quis tempus tortor eros non ipsum. Mauris convallis augue ac sapien. In scelerisque dignissim elit. Donec consequat semper lectus. Sed in quam. Nunc molestie hendrerit ipsum. Curabitur elit risus, rhoncus ut, mattis a, convallis eu, neque. Morbi luctus est sit amet nunc. In nisl. Donec magna libero, aliquet eu, vestibulum ut, mollis sed, felis.";

    ob_start();
?>
<style type="text/css">
div { width: 150mm; border: solid 1mm #770000; margin: 0; padding: 0; font-size: 4mm; }
</style>
<blockquote><?php echo $phrase; ?></blockquote>
<div style="text-align: justify"><?php echo $phrase; ?></div>
<div style="text-align: justify"><?php echo strip_tags($phrase); ?></div>
<div style="text-align: left"><?php echo $phrase; ?></div>
<div style="text-align: center"><?php echo $phrase; ?></div>
<div style="text-align: right"><?php echo $phrase; ?></div>
<?php
    // convert to PDF
    require_once(dirname(__FILE__).'/html2pdf.class.php');
    $html2pdf = new HTML2PDF();
    $html2pdf->writeHTML(ob_get_clean());
    $html2pdf->Output();
