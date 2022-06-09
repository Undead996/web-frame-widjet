<script>
    const data = <?php print json_encode($data, JSON_UNESCAPED_UNICODE);?>;
    const devDefaultForm = <?php echo '"'.TEMPLATE_CARD_FORM.'"';?>;
</script>
<?php
require './SPA/index.html';
?>
