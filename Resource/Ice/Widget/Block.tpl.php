<div id="<?= $widgetId ?>"
     class="<?= $widgetClass ?> form<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php if (isset($parts['breadcrumbs'])) : ?>
        <?= $parts['breadcrumbs']['content'] ?>
        <?php unset($parts['breadcrumbs']); ?>
    <?php endif; ?>
    <?php if (isset($parts['header'])) : ?>
        <?= $parts['header']['content'] ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>

    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</div>