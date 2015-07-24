<?php if (!empty($header)) :?><h3><?= $header ?></h3><?php endif; ?>
<?php if (!empty($description)) :?><h5><?= $description ?></h5><?php endif; ?>
<ol id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
    class="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>  nav <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>
    data-url='<?= $dataUrl ?>'
    data-json='<?= $dataJson ?>'
    data-action='<?= $dataAction ?>'
    data-block='<?= $dataBlock ?>'>
    <?php foreach ($parts as $item) : ?>
        <?= $item ?>
    <?php endforeach; ?>
</ol>
