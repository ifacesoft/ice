<button id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
        class="btn <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?= $component->getButtonType() == 'submit' ? $component->getButtonType() : $component->getEventAttributesCode() ?>
        data-name="<?= $component->getName() ?>"
        data-for="<?= $component->getWidgetId() ?>"
        <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
        type="<?= $component->getButtonType() ?>"><?= $component->getLabel() ?></button>
