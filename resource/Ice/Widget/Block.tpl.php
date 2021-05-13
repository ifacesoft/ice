<div id="<?= $widgetId ?>"
     class="<?= $widgetClass ?> <?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     style="<?= $style ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
>
    <?php $parts = reset($result) ?>
    <?php if (isset($parts['breadcrumbs'])) : ?>
        <?= $parts['breadcrumbs']->render() ?>
        <?php unset($parts['breadcrumbs']); ?>
    <?php endif; ?>
    <?php if (isset($parts['header'])) : ?>
        <?= $parts['header']->render() ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>

    <?php foreach ($parts as $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</div>