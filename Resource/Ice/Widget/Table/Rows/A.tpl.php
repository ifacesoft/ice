<a id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
   class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
   <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
   data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
   <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   data-name="<?= $component->getName() ?>"
   data-params='<?= $component->getDataParams() ?>'
   data-for="<?= $component->getWidgetId() ?>" disabled="disabled">
    <?php if (isset($oneToMany)) : ?>
        <?= implode(', ', $oneToMany) ?>
    <?php elseif (isset($manyToMany)) : ?>
        <?= $manyToMany ?>
    <?php elseif (isset($params[$title])) : ?>
        <?= $params[$title] ?>
    <?php else : ?>
        <?= $title ?>
    <?php endif; ?>
</a>