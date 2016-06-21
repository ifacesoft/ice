<li id="<?= $widgetId ?>"
     class="<?= $widgetClass ?><?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php foreach (reset($result) as $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</li>
