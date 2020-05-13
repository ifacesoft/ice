<h3 <?= $component->getClassAttribute() ?>>
    <a <?= $component->getIdAttribute() ?>
        <?= $component->getEventAttributesCode() ?>
        <?= $component->getHtmlTagAttributes() ?>
        href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
    ><?= $component->getValue() ?></a>
</h3>