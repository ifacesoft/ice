<li id="<?= $component->getPartId() ?>"
    class="<?= $component->getComponentName() ?>
<?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if ($component->isActive()) : ?> active<?php endif; ?>
">
    <a href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
        <?= $component->getEventAttributesCode() ?>
       data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></a>
</li>