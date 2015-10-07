<div id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
     class="Widget_<?= $widgetClassName ?><?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
     data-action='<?= $dataAction ?>'
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $dataFor ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</div>
