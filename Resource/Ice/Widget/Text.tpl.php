<?php if (isset($params[$label])) : ?>
    <?= $params[$label] ?>
<?php else : ?>
    <?= $label ?>
<?php endif; ?>