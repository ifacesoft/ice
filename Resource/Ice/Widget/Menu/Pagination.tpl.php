<ul id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
    class="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?> pagination <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>
    data-url='<?= $dataUrl ?>'
    data-json='<?= $dataJson ?>'
    data-action='<?= $dataAction ?>'
    data-block='<?= $dataBlock ?>'>
    <?php foreach ($parts as $widgetPartName => $item) : ?>
        <?php if (in_array($widgetPartName, ['first', 'fastFastPrev', 'fastPrev', 'prev', 'curr', 'next', 'nextNext', 'fastNext', 'fastFastNext', 'last'])) : ?>
            <?= $item ?>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
