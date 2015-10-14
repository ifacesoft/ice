<ol id="<?= $widgetId ?>"
    class="<?= $widgetClass ?> breadcrumb<?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
    <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
    data-widget='<?= $dataWidget ?>'
    data-for="<?= $parentWidgetId ?>"
>
    <?= implode('', array_column(reset($result), 'content')) ?>
</ol>