<h4 <?= $component->getClassAttribute() ?>>
    <a <?= $component->getIdAttribute() ?>
        <?= $component->getEventAttributesCode() ?>
        <?= $component->getHtmlTagAttributes() ?>
        href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
    ><?= $component->getLabel() ?></a>
</h4>