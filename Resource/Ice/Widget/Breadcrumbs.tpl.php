<ol id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
    class="Widget_<?= $widgetClassName ?> breadcrumb<?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
    data-url='<?= $dataUrl ?>'
    data-action='<?= $dataAction ?>'
    data-view='<?= $dataView ?>'
    data-widget='<?= $dataWidget ?>'
    data-token="<?= $dataToken ?>"
    data-for="<?= $dataFor ?>"
>
    <?= implode('', array_column(reset($result), 'content')) ?>
</ol>