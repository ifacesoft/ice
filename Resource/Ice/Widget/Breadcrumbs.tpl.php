<ol id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
    class="Widget_<?= $widgetClassName ?> breadcrumb<?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
    <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $dataFor ?>"
>
    <?= implode('', array_column(reset($result), 'content')) ?>
</ol>