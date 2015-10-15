<div id="<?= $widgetId ?>"
    class="<?= $widgetClass ?><?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
    data-widget='<?= $dataWidget ?>'
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