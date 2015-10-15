<nav id="<?= $widgetId ?>"
     class="<?= $widgetClass ?> navbar <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
     data-widget='<?= $dataWidget ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <div class="container-fluid">
        <?php $parts = reset($result) ?>
        <?php foreach ($parts as $part) : ?>
            <?= $part['content'] ?>
        <?php endforeach; ?>
    </div>
</nav>