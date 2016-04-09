<nav id="<?= $widgetId ?>"
     class="<?= $widgetClass ?> navbar <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <div class="container-fluid">
        <?php $parts = reset($result) ?>
        <?php foreach ($parts as $part) : ?>
            <?= $widget->renderPart($part) ?>
        <?php endforeach; ?>
    </div>
</nav>