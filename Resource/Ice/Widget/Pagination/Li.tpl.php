<?php if (!empty($options['prev'])) : ?>
    <li><span><?= $options['prev'] ?></span></li><?php endif; ?>
    <li class="menu_item<?php if ($component->isActive()) : ?> active<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>">
        <a id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
           href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
           <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
           data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
           data-name="<?= $component->getName() ?>"
           data-params='<?= $component->getDataParams() ?>'
           data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></a>
    </li>
<?php if (!empty($options['next'])) : ?>
    <li><span><?= $options['next'] ?></span></li><?php endif; ?>