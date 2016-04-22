<li>
    <a <?= $component->getIdAttribute() ?> <?= $component->getClassAttribute() ?>
       href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
    ><?= $component->getValue() ?></a>
    <?= $options['nav'] ?>
</li>