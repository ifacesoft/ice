<h2 <?= $component->getIdAttribute() ?>
    <?= $component->getClassAttribute() ?>
    <?= $component->getEventAttributesCode() ?>
        style="<?= $component->getOption('style', '') ?>"
><?= $component->getValue() ?></h2>