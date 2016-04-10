<h4
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-name="<?= $component->getName() ?>"
    data-params='<?= $component->getDataParams() ?>'
    data-for="<?= $component->getWidgetId() ?>"
>
    <a href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>>
        <?php if (isset($params[$label])) : ?><?= $params[$label] ?><?php else : ?><?= $component->getLabel() ?><?php endif; ?>
    </a>
</h4>