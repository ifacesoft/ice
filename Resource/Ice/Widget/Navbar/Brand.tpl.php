<div
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="navbar-header <?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-for="<?= $component->getWidgetId() ?>">
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
       <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>><?= $component->getLabel() ?></a>
</div>