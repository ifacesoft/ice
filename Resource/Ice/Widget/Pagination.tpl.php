<?php if ($parts) : ?>
    <ul id="<?= $widgetId ?>"
        class="<?= $widgetClass ?> pagination<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
        data-widget='<?= $dataWidget ?>'
        data-params='<?= $dataParams ?>'
        data-for="<?= $parentWidgetId ?>"
    >
        <?php foreach (reset($result) as $part) : ?>
            <?= $part->render() ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>