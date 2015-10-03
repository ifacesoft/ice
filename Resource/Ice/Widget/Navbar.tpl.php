<nav id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
     class="Widget_<?= $widgetClassName ?> navbar <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
     <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
     data-widget='<?= $dataWidget ?>'
     data-for="<?= $dataFor ?>"
>
    <div class="container-fluid">
        <?php $parts = reset($result) ?>
        <?php foreach ($parts as $part) : ?>
            <?= $part['content'] ?>
        <?php endforeach; ?>
    </div>
</nav>