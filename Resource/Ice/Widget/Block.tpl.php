<div id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
    class="Widget_<?= $widgetClassName ?><?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
    <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $dataFor ?>"
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