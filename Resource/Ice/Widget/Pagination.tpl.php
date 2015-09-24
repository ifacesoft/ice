<ul id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
    class="Widget_<?= $widgetClassName ?> pagination <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    data-url='<?= $dataUrl ?>'
    data-action='<?= $dataAction ?>'
    data-view='<?= $dataView ?>'
    data-widget='<?= $dataWidget ?>'
    data-token="<?= $dataToken ?>"
    data-for="<?= $dataFor ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $widgetPartName => $part) : ?>
        <?php if (in_array($widgetPartName, ['first', 'fastFastPrev', 'fastPrev', 'prev', 'curr', 'next', 'nextNext', 'fastNext', 'fastFastNext', 'last'])) : ?>
            <?= $part['content'] ?>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
