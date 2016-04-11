<p
    id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?= $component->getEventAttributesCode() ?>
    data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></p>
