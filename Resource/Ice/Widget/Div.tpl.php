<div
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
    data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
    data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></div>
