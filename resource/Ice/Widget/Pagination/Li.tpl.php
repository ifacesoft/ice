<?php if (!empty($component->getOption('prev'))) : ?>
    <li><span><?= $component->getOption('prev') ?></span></li>
<?php endif; ?>
    <li <?= $component->getClassAttribute('menu_item') ?>>
        <a <?= $component->getIdAttribute() ?>
            href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
            <?= $component->getEventAttributesCode() ?>
        ><?= $component->getValue() ?></a>
    </li>
<?php if (!empty($component->getOption('next'))) : ?>
    <li><span><?= $component->getOption('next') ?></span></li>
<?php endif; ?>