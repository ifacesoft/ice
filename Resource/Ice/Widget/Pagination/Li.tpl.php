<?php if (!empty($options['prev'])) : ?>
    <li><span><?= $options['prev'] ?></span></li>
<?php endif; ?>
    <li <?= $component->getClassAttribute('menu_item') ?>>
        <a <?= $component->getIdAttribute() ?>
            href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
            <?= $component->getEventAttributesCode() ?>
        ><?= $component->getLabel() ?></a>
    </li>
<?php if (!empty($options['next'])) : ?>
    <li><span><?= $options['next'] ?></span></li>
<?php endif; ?>