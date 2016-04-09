<a id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
   href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $component->getComponentName() ?>"
   <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
   data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
   <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   data-name="<?= $component->getName() ?>"
   data-params='<?= $component->getParams() ?>'
   data-for="<?= $component->getWidgetId() ?>"><?php if (isset($params[$label])) : ?><?= $params[$label] ?><?php else : ?><?= $component->getLabel() ?><?php endif; ?></a>