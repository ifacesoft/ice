<ul id="<?= $widgetId ?>"
     class="<?= $widgetClass ?><?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>"
    style="padding-left: 10px;"
><!-- delete style attribute -->
    <?php foreach (reset($result) as $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</ul>
