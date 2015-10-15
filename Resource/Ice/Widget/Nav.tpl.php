<?php $parts = reset($result) ?>
<?php if (isset($parts['header'])) : ?>
    <?= $parts['header']['content'] ?>
    <?php unset($parts['header']); ?>
<?php endif; ?>

<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> nav<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</ul>
