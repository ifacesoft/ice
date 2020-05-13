<a <?= $component->getIdAttribute() ?>
    <?= $component->getClassAttribute() ?>
    <?= $component->getEventAttributesCode() ?>
    <?= $component->getHtmlTagAttributes() ?>
    href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
><?= $component->getValue() ?></a>