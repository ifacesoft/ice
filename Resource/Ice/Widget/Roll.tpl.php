<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?>"
    data-widget='<?= $dataWidget ?>'
    data-params='<?= $dataParams ?>'
    data-for="<?= $parentWidgetId ?>">
    <?php foreach ($result as $offset => $parts) : ?>
        <li>
            <?php if ($isShowCount) : ?>
                <?= $offset ?>
            <?php endif; ?>
            <?php foreach ($parts as $part) : ?>
                <?= $part->render() ?>
            <?php endforeach; ?>
        </li>
    <?php endforeach; ?>
</ul>
