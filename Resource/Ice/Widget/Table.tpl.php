<?php $parts = reset($result) ?>
<?php if (isset($parts['header'])) : ?>
    <?= $parts['header']['content'] ?>
    <?php unset($parts['header']); ?>
<?php endif; ?>
<?php
$pagination = isset($parts['pagination']) ? $parts['pagination']['content'] : '';
if (isset($parts['pagination'])) {
    unset($parts['pagination']);
}
?>
<?= $pagination ?>
<table id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
       class="Widget_<?= $widgetClassName ?> table<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
       data-url='<?= $dataUrl ?>'
       data-action='<?= $dataAction ?>'
       data-widget='<?= $dataWidget ?>'
       data-for="<?= $dataFor ?>"
>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</table>
<?= $pagination ?>
