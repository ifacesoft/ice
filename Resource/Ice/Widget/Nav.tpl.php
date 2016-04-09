<?php $parts = reset($result) ?>
<?php foreach ($parts as $partName => $part) : ?>
    <?php if ($part instanceof \Ice\WidgetComponent\Widget && $part->getWidget() instanceof \Ice\Widget\Header) : ?>
        <?= $widget->renderPart($part) ?>
        <?php unset($parts[$partName]); ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($parts) : ?>
<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> nav<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
    data-widget='<?= $dataWidget ?>'
    data-params='<?= $dataParams ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php foreach ($parts as $part) : ?>
        <?= $widget->renderPart($part) ?>
    <?php endforeach; ?>
</ul>
<?php endif; ?>