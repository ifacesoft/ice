<ul id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
    class="Widget_<?= $widgetClassName ?>  nav <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    data-url='<?= $dataUrl ?>'
    data-action='<?= $dataAction ?>'
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $dataFor ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $item) : ?>
        <?= $item ?>
    <?php endforeach; ?>
</ul>
