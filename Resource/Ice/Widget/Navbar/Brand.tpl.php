<div
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="navbar-header <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-name="<?= $component->getName() ?>"
    data-params='<?= $component->getDataParams() ?>'
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
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
       data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
       <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>><?= $component->getLabel() ?></a>
</div>