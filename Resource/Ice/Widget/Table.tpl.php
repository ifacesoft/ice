<div id="<?= $widgetId ?>"
     <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
     data-widget='<?= $dataWidget ?>'
     data-for="<?= $parentWidgetId ?>">
    <?php $parts = reset($result) ?>
    <?php if (isset($parts['header'])) : ?>
        <?= $parts['header']['content'] ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>
    <?php
    $pagination = isset($parts['pagination']) ? $parts['pagination']['content'] : '';
    if (isset($parts['pagination'])) {
        unset($parts['pagination']);
    }
    ?>
    <?= $pagination ?>
    <table class="<?= $widgetClass ?> table<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>">
        <?php foreach ($parts as $part) : ?>
            <?= $part['content'] ?>
        <?php endforeach; ?>
    </table>
    <?= $pagination ?>
</div>