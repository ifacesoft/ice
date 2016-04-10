<li id="<?= $component->getPartId() ?>"
    class="<?= $component->getComponentName() ?>
<?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if ($component->isActive()) : ?> active<?php endif; ?>
"
    <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
    data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
    data-name="<?= $component->getName() ?>"
    data-params='<?= $component->getDataParams() ?>'
    data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?>
</li>