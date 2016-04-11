<li id="<?= $component->getPartId() ?>"
    class="<?= $component->getComponentName() ?>
<?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if ($component->isActive()) : ?> active<?php endif; ?>
"
    <?= $component->getEventAttributesCode() ?>
    data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?>
</li>