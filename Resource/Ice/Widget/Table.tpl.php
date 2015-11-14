<div id="<?= $widgetId ?>"
     data-widget='<?= $dataWidget ?>'
     data-for="<?= $parentWidgetId ?>">
    <?php $parts = reset($result) ?>
    <?php if (isset($parts['header'])) : ?>
        <?= $widget->renderPart($parts['header']) ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>
    <?php
    $pagination = isset($parts['pagination']) ? $widget->renderPart($parts['pagination']) : '';
    if (isset($parts['pagination'])) {
        unset($parts['pagination']);
    }
    ?>
    <?= $pagination ?>
    <table class="<?= $widgetClass ?> table<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>">
        <?php foreach ($parts as $part) : ?>
            <?= $widget->renderPart($part) ?>
        <?php endforeach; ?>
    </table>
    <?= $pagination ?>
</div>