<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> pagination <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $widgetPartName => $part) : ?>
        <?php if (in_array($widgetPartName, ['first', 'fastFastPrev', 'fastPrev', 'prev', 'curr', 'next', 'nextNext', 'fastNext', 'fastFastNext', 'last'])) : ?>
            <?= $part['content'] ?>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
