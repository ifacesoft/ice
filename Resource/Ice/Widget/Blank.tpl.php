<?php $parts = reset($result) ?>
<?php foreach ($parts as $part) : ?>
    <?= $widget->renderPart($part) ?>
<?php endforeach; ?>
