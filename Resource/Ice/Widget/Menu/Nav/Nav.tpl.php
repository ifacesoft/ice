<li>
    <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $component->getComponentName() ?>"><?= $component->getLabel() ?></a>
    <?= $options['nav'] ?>
</li>