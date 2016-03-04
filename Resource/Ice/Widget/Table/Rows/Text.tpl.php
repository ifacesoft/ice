<?php if (isset($params[$title])) : ?>
    <?= $params[$title] ?>
<?php else : ?>
    <?= $title ?>
<?php endif; ?>