<li>
    <a <?= $component->getIdAttribute() ?> <?= $component->getClassAttribute() ?>
       href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
    ><?= $component->getLabel() ?></a>
    <?= $options['nav'] ?>
</li>