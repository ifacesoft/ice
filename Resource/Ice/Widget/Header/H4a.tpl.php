<h4
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-name="<?= $component->getName() ?>"
    data-params='<?= $component->getParams() ?>'
    data-for="<?= $component->getWidgetId() ?>"
>
    <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $component->getComponentName() ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>>
        <?php if (isset($params[$label])) : ?><?= $params[$label] ?><?php else : ?><?= $component->getLabel() ?><?php endif; ?>
    </a>
</h4>