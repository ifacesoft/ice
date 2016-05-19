<nav id="<?= $widgetId ?>"
     class="<?= $widgetClass ?> navbar <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <div class="container-fluid">
        <?php foreach (reset($result) as $part) : ?>
            <?= $part->render() ?>
        <?php endforeach; ?>
    </div>
</nav>