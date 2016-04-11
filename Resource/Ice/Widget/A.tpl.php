<a id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
   class="<?= $component->getComponentName() ?><?php if (!empty($component->getOption('classes'))) : ?> <?= $component->getOption('classes') ?><?php endif; ?>"
   <?= $component->getEventAttributesCode() ?>
   <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></a>