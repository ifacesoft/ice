<div <?= $component->getIdAttribute() ?> <?= $component->getClassAttribute('navbar-header') ?>>
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
            aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand"
       href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
        <?= $component->getEventAttributesCode() ?>
       <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>><?= $component->getValue() ?></a>
</div>