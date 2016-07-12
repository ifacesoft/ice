<?php $parts = reset($result) ?>

<tr id="<?= $widgetId ?>"
       data-widget='<?= $dataWidget ?>'
       data-params='<?= $dataParams ?>'
       data-for="<?= $parentWidgetId ?>"
       class="<?= $widgetClass ?> table<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>">
    <?php foreach ($parts as $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</tr>