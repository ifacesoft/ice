<div id="<?= $widgetId ?>"
     class="<?= $widgetClass ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php foreach (reset($result) as $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</div>