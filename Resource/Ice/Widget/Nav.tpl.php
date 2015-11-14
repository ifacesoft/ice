<?php $parts = reset($result) ?>
<?php if (isset($parts['header'])) : ?>
    <?= $widget->renderPart($parts['header']) ?>
    <?php unset($parts['header']); ?>
<?php endif; ?>

<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> nav<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php foreach ($parts as $part) : ?>
        <?= $widget->renderPart($part) ?>
    <?php endforeach; ?>
</ul>
