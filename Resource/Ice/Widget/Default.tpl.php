<div id="<?= $widgetId ?>"
     class="<?= $widgetClass ?><?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     data-action='<?= $dataAction ?>'
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</div>
