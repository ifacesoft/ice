<?php $parts = reset($result) ?>
<?php foreach ($parts as $partName => $part) : ?>
    <?php if (isset($part['options']['widget']) && $part['options']['widget'] instanceof \Ice\Widget\Header) : ?>
        <?= $widget->renderPart($part) ?>
        <?php unset($parts[$partName]); ?>
    <?php endif; ?>
<?php endforeach; ?>

<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> nav<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php foreach ($parts as $part) : ?>
        <?= $widget->renderPart($part) ?>
    <?php endforeach; ?>
</ul>
