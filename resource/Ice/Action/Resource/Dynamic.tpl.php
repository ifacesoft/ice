<?php foreach ($css as $cssResource) : ?>
    <link type="text/css" rel="stylesheet" href="<?= $cssResource ?>"/>
<?php endforeach; ?>
<?php foreach ($js as $jsResource) : ?>
    <script type="text/javascript" src="<?= $jsResource ?>"></script>
<?php endforeach; ?>
