<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> pagination<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
    data-widget='<?= $dataWidget ?>'
    data-params='<?= $dataParams ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $part) : ?>
        <?= $widget->renderPart($part) ?>
    <?php endforeach; ?>
</ul>
