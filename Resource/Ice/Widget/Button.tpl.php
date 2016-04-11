<button id="<?= $component->getPartId() ?>"
        class="btn <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?= $component->getEventAttributesCode() ?>
        data-name="<?= $component->getName() ?>"
        data-for="<?= $component->getWidgetId() ?>"
        type="button"><?= $component->getLabel() ?></button>
