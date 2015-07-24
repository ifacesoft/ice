<div role="tablist" aria-multiselectable="true"
     id="accordion_<?= $menuName ?>_<?= $token ?>"
     class="panel-group Form_<?= $menuName ?><?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
     <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>
     data-url='<?= $dataUrl ?>'
     data-json='<?= $dataJson ?>'
     data-action='<?= $dataAction ?>'
     data-block='<?= $dataBlock ?>'>
    <?php foreach ($parts as $item) : ?>
        <?= $item ?>
    <?php endforeach; ?>
</div>