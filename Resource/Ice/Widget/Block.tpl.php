<div id="<?= $widgetId ?>"
     class="<?= $widgetClass ?> form<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php if (isset($parts['breadcrumbs'])) : ?>
        <?= $widget->renderPart($parts['breadcrumbs']) ?>
        <?php unset($parts['breadcrumbs']); ?>
    <?php endif; ?>
    <?php if (isset($parts['header'])) : ?>
        <?= $widget->renderPart($parts['header']) ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>

    <?php foreach ($parts as $part) : ?>
        <?= $widget->renderPart($part) ?>
    <?php endforeach; ?>
</div>