<a id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
   class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?= $component->getEventAttributesCode() ?>
   <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   data-for="<?= $component->getWidgetId() ?>"
><?= $component->getValue() ?></a>