<a <?= $component->getIdAttribute() ?>
    <?= $component->getClassAttribute() ?>
    <?= $component->getEventAttributesCode() ?>
    href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
    <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
><?= $component->getValue() ?></a>