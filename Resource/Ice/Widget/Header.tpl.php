<div id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
     class="Widget_<?= $widgetClassName ?>"
     data-url='<?= $dataUrl ?>'
     data-action='<?= $dataAction ?>'
     data-view='<?= $dataView ?>'
     data-widget='<?= $dataWidget ?>'
     data-token="<?= $dataToken ?>"
     data-for="<?= $dataFor ?>"
>
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</div>