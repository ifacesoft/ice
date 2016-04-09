<?php if (isset($params[$label])) : ?>
    <?= $params[$label] ?>
<?php else : ?>
    <?= $component->getLabel() ?>
<?php endif; ?>