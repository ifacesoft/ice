<ol id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
    class="Widget_<?= $widgetClassName ?>  nav <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    data-url='<?= $dataUrl ?>'
    data-action='<?= $dataAction ?>'
    data-view='<?= $dataView ?>'
    data-widget='<?= $dataWidget ?>'
    data-token="<?= $dataToken ?>"
    data-for="<?= $dataFor ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $item) : ?>
        <?= $item ?>
    <?php endforeach; ?>
</ol>
