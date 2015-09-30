<div id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
     class="Widget_<?= $widgetClassName ?>"
     data-url='<?= $dataUrl ?>'
     data-action='<?= $dataAction ?>'
     data-widget='<?= $dataWidget ?>'
     data-for="<?= $dataFor ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</div>