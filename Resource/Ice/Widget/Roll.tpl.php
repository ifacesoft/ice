<ul id="<?= $widgetId ?>"
    class="<?= $widgetClass ?>"
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>">
    <?php foreach ($result as $offset => $parts) : ?>
        <li>
            <?php if ($isShowCount) : ?>
                <?= $offset ?>
            <?php endif; ?>
            <?php foreach ($parts as $part) : ?>
                <?= $widget->renderPart($part) ?>
            <?php endforeach; ?>
        </li>
    <?php endforeach; ?>
</ul>
