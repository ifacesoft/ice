<div id="<?= $widgetId ?>"
     class="<?= $widgetClass ?>"
     data-widget='<?= $dataWidget ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</div>