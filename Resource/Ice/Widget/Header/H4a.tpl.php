<h4
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-for="<?= $component->getWidgetId() ?>"
>
    <a href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
        <?= $component->getEventAttributesCode() ?>
    ><?php if (isset($params[$label])) : ?><?= $params[$label] ?><?php else : ?><?= $component->getLabel() ?><?php endif; ?></a>
</h4>