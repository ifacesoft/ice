<nav id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
     class="Widget_<?= $widgetClassName ?> navbar <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
     data-url='<?= $dataUrl ?>'
     data-action='<?= $dataAction ?>'
     data-view='<?= $dataView ?>'
     data-widget='<?= $dataWidget ?>'
     data-token="<?= $dataToken ?>"
     data-for="<?= $dataFor ?>"
>
    <div class="container-fluid">
        <?php $parts = reset($result) ?>
        <?php foreach ($parts as $part) : ?>
            <?= $part['content'] ?>
        <?php endforeach; ?>
    </div>
</nav>